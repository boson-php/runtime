<?php

declare(strict_types=1);

namespace Boson;

use Boson\Api\ApplicationExtension;
use Boson\Api\Dialog\ApplicationDialog;
use Boson\Api\DialogApiInterface;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Event\ApplicationStarted;
use Boson\Event\ApplicationStarting;
use Boson\Event\ApplicationStopped;
use Boson\Event\ApplicationStopping;
use Boson\Exception\NoDefaultWindowException;
use Boson\Internal\ApplicationPoller;
use Boson\Internal\BootHandler\BootHandlerInterface;
use Boson\Internal\BootHandler\WindowsDetachConsoleBootHandler;
use Boson\Internal\DeferRunner\DeferRunnerInterface;
use Boson\Internal\DeferRunner\NativeShutdownFunctionRunner;
use Boson\Internal\QuitHandler\PcntlQuitHandler;
use Boson\Internal\QuitHandler\QuitHandlerInterface;
use Boson\Internal\QuitHandler\WindowsQuitHandler;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\ThreadsCountResolver;
use Boson\Shared\Marker\BlockingOperation;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\WebView;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Manager\WindowManager;
use Boson\Window\Window;
use FFI\CData;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @api
 */
final class Application implements EventListenerInterface
{
    use EventListenerProvider;

    /**
     * List of error types that block the application
     * from starting automatically.
     *
     * @var non-empty-list<int>
     */
    private const array NOT_RUNNABLE_ERROR_TYPES = [
        \E_ERROR,
        \E_PARSE,
        \E_CORE_ERROR,
        \E_COMPILE_ERROR,
        \E_USER_ERROR,
    ];

    /**
     * Unique application identifier.
     *
     * It is worth noting that the destruction of this object
     * from memory (deallocation using PHP GC) means the physical
     * destruction of all data associated with it, including unmanaged.
     */
    public readonly ApplicationId $id;

    /**
     * Gets windows list and methods for working with windows.
     */
    public readonly WindowManager $windows;

    /**
     * Application-aware event listener & dispatcher.
     */
    private readonly EventListener $listener;

    /**
     * Gets access to the Dialog API of the application.
     */
    public readonly DialogApiInterface $dialog;

    /**
     * Provides more convenient and faster access to the
     * {@see WindowManager::$default} subsystem from
     * child {@see $windows} property.
     *
     * @uses WindowManager::$default Default (first) window of the windows list
     */
    public Window $window {
        /**
         * Gets the default window of the application.
         *
         * @throws NoDefaultWindowException in case the default window was
         *         already closed and removed earlier
         */
        get => $this->windows->default
            ?? throw NoDefaultWindowException::becauseNoDefaultWindow();
    }

    /**
     * Provides more convenient and faster access to the {@see Window::$webview}
     * subsystem from {@see $window} property.
     *
     * @uses Window::$webview The webview of the default (first) window
     */
    public WebView $webview {
        /**
         * Gets the WebView instance associated with the default window.
         *
         * @throws NoDefaultWindowException in case the default window was
         *         already closed and removed earlier
         */
        get => $this->window->webview;
    }

    /**
     * Gets debug mode of an application.
     *
     * Unlike {@see ApplicationCreateInfo::$debug}, it contains
     * a REAL debug value, including possibly automatically derived.
     *
     * Contains {@see true} in case of debug mode
     * is enabled or {@see false} instead.
     */
    public readonly bool $isDebug;

    /**
     * Gets running state of an application.
     *
     * Contains {@see true} in case of application is running
     * or {@see false} instead.
     */
    public private(set) bool $isRunning = false;

    /**
     * Indicates whether the application was ever running
     */
    private bool $wasEverRunning = false;

    /**
     * Shared WebView API library.
     *
     * @internal Not safe, you can get segfault, use
     *           this low-level API at your own risk!
     */
    public readonly LibSaucer $api;

    /**
     * Gets an internal application poller to unlock the
     * webview process workflow.
     */
    public readonly ApplicationPollerInterface $poller;

    /**
     * @param EventDispatcherInterface|null $dispatcher an optional event
     *        dispatcher for handling application events
     */
    public function __construct(
        /**
         * Gets an information DTO about the application
         * with which it was created.
         */
        public readonly ApplicationCreateInfo $info = new ApplicationCreateInfo(),
        ?EventDispatcherInterface $dispatcher = null,
        /**
         * @var list<BootHandlerInterface>
         */
        private readonly array $bootHandlers = [
            new WindowsDetachConsoleBootHandler(),
        ],
        /**
         * @var list<QuitHandlerInterface>
         */
        private readonly array $quitHandlers = [
            new WindowsQuitHandler(),
            new PcntlQuitHandler(),
        ],
        /**
         * @var list<DeferRunnerInterface>
         */
        private readonly array $deferRunners = [
            new NativeShutdownFunctionRunner(),
        ],
    ) {
        // Initialization Application's fields and properties
        $this->api = self::createLibSaucer($info->library);
        $this->isDebug = self::createIsDebugParameter($info->debug);
        $this->listener = self::createEventListener($dispatcher);
        $this->id = self::createApplicationId($this->api, $this->info->name, $this->info->threads);
        $this->poller = self::createApplicationPoller($this->api, $this);
        $this->windows = self::createWindowManager($this->api, $this, $info, $this->listener);

        // Initialization of Application's API
        $this->dialog = $this->createApplicationExtension(ApplicationDialog::class);

        // Register Application's subsystems
        $this->registerSchemes();
        $this->registerDefaultEventListeners();
        $this->registerQuitHandlers();
        $this->registerDeferRunner();

        // Boot the Application
        $this->boot();
    }

    /**
     * Creates a new instance of {@see LibSaucer} that provides access to the
     * native WebView API library. The returned object allows interacting with
     * the underlying WebView (Saucer) functionality through FFI bindings.
     *
     * The library path can be automatically detected if not explicitly specified.
     *
     * This method is responsible for initializing the core WebView functionality
     * that powers the application's window and web content capabilities.
     *
     * @param non-empty-string|null $library Optional path to the WebView library
     */
    private static function createLibSaucer(?string $library): LibSaucer
    {
        return new LibSaucer($library);
    }

    /**
     * Resolves the debug mode state for the application based on the provided
     * configuration and environment settings.
     *
     * This method uses php.ini dev mode detection to determine the actual debug
     * state, taking into account both explicit configuration and automatic
     * environment detection.
     */
    private static function createIsDebugParameter(?bool $debug): bool
    {
        if ($debug === null) {
            $debug = false;

            /**
             * Enable debug mode if "zend.assertions" is 1.
             *
             * @link https://www.php.net/manual/en/function.assert.php
             */
            assert($debug = true);
        }

        return $debug;
    }

    /**
     * Creates a new instance of {@see ApplicationPollerInterface} that manages
     * the application's event loop and process workflow.
     */
    private function createApplicationPoller(LibSaucer $api, Application $ctx): ApplicationPollerInterface
    {
        return new ApplicationPoller($api, $ctx);
    }

    /**
     * @template TArgApiProvider of ApplicationExtension
     *
     * @param class-string<TArgApiProvider> $class
     *
     * @return TArgApiProvider
     */
    private function createApplicationExtension(string $class): ApplicationExtension
    {
        return new $class(
            api: $this->api,
            context: $this,
            listener: $this->listener,
        );
    }

    /**
     * Boot the application.
     */
    private function boot(): void
    {
        foreach ($this->bootHandlers as $handler) {
            $handler->boot();
        }
    }

    /**
     * Creates a new window manager instance.
     *
     * This method initializes and returns a {@see WindowManager} object
     * that is responsible for managing all application windows.
     */
    private static function createWindowManager(
        LibSaucer $api,
        Application $app,
        ApplicationCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ): WindowManager {
        return new WindowManager(
            api: $api,
            app: $app,
            info: $info->window,
            dispatcher: $dispatcher,
        );
    }

    /**
     * Register list of protocol names that will be
     * intercepted by the application.
     */
    private function registerSchemes(): void
    {
        foreach ($this->info->schemes as $scheme) {
            $this->api->saucer_register_scheme($scheme);
        }
    }

    /**
     * Registers default event listeners for the application.
     *
     * This includes handling window close events.
     */
    private function registerDefaultEventListeners(): void
    {
        $this->listener->addEventListener(WindowClosed::class, $this->onWindowClose(...));
        $this->listener->addEventListener(ApplicationStarted::class, $this->onApplicationStarted(...));
    }

    /**
     * Handles the window close event.
     *
     * If {@see $quitOnClose} is enabled ({@see true}) and
     * all windows are closed, the application will quit.
     */
    private function onWindowClose(): void
    {
        if ($this->info->quitOnClose && $this->windows->count() === 0) {
            $this->quit();
        }
    }

    /**
     * Handles an application started event.
     *
     * Resolve main window lazy proxy (facade).
     */
    private function onApplicationStarted(): void
    {
        // Resolve main window lazy proxy (facade)
        $_ = $this->window->isClosed;
    }

    /**
     * Creates local (application-aware) event listener
     * based on the provided dispatcher.
     */
    private static function createEventListener(?EventDispatcherInterface $dispatcher): EventListener
    {
        if ($dispatcher === null) {
            return new EventListener();
        }

        return new DelegateEventListener($dispatcher);
    }

    /**
     * Creates a new application ID
     *
     * @param non-empty-string $name
     * @param int<1, max>|null $threads
     */
    private static function createApplicationId(LibSaucer $api, string $name, ?int $threads): ApplicationId
    {
        return ApplicationId::fromAppHandle(
            api: $api,
            handle: self::createApplicationPointer($api, $name, $threads),
            name: $name,
        );
    }

    /**
     * Creates a new application instance pointer.
     *
     * @param non-empty-string $name
     * @param int<1, max>|null $threads
     */
    #[RequiresDealloc]
    private static function createApplicationPointer(LibSaucer $api, string $name, ?int $threads): CData
    {
        $options = self::createApplicationOptionsPointer($api, $name, $threads);

        try {
            return $api->saucer_application_init($options);
        } finally {
            $api->saucer_options_free($options);
        }
    }

    /**
     * Creates a new application options pointer.
     *
     * @param non-empty-string $name
     * @param int<1, max>|null $threads
     */
    #[RequiresDealloc]
    private static function createApplicationOptionsPointer(LibSaucer $api, string $name, ?int $threads): CData
    {
        $options = $api->saucer_options_new($name);

        $threads = ThreadsCountResolver::resolve($threads);

        if ($threads !== null) {
            $api->saucer_options_set_threads($options, $threads);
        }

        return $options;
    }

    /**
     * Registers quit handlers if they haven't been registered yet.
     *
     * This ensures that the application can be properly terminated.
     */
    private function registerQuitHandlers(): void
    {
        foreach ($this->quitHandlers as $handler) {
            if ($handler->isSupported === false) {
                continue;
            }

            // Register EVERY quit handler
            $handler->register($this->quit(...));
        }
    }

    /**
     * Registers a defer runner if none has been registered yet.
     *
     * This allows the application to be started automatically
     * after script execution.
     */
    private function registerDeferRunner(): void
    {
        if ($this->info->autorun === false) {
            return;
        }

        foreach ($this->deferRunners as $runner) {
            if ($runner->isSupported === false) {
                continue;
            }

            // Register FIRST supported deferred runner
            $runner->register($this->runIfNotEverRunning(...));
            break;
        }
    }

    /**
     * Runs the application if it has never been run before.
     *
     * This is used by the defer runner to start
     * the application automatically.
     */
    private function runIfNotEverRunning(): void
    {
        if ($this->wasEverRunning) {
            return;
        }

        $this->run();
    }

    /**
     * Dispatches an intention to launch an application and returns a {@see bool}
     * result: whether to start the application or not.
     */
    private function shouldNotStart(): bool
    {
        $error = \error_get_last();

        // The application cannot be run if there are errors.
        if (\in_array($error['type'] ?? 0, self::NOT_RUNNABLE_ERROR_TYPES, true)) {
            return true;
        }

        $this->listener->dispatch($intention = new ApplicationStarting($this));

        return $intention->isCancelled;
    }

    /**
     * Runs the application, starting the main event loop.
     *
     * This method blocks main thread until the
     * application is quit.
     *
     * @api
     */
    #[BlockingOperation]
    public function run(): void
    {
        if ($this->isRunning || $this->shouldNotStart()) {
            return;
        }

        $this->isRunning = true;
        $this->wasEverRunning = true;

        $this->listener->dispatch(new ApplicationStarted($this));

        while ($this->poller->next()) {
            \usleep(1);
        }
    }

    /**
     * Dispatches an intention to stop an application and returns a {@see bool}
     * result: whether to stop the application or not.
     */
    private function shouldNotStop(): bool
    {
        $this->listener->dispatch($intention = new ApplicationStopping($this));

        return $intention->isCancelled;
    }

    /**
     * Quits the application, stopping the main
     * loop and releasing resources.
     *
     * @api
     */
    public function quit(): void
    {
        if ($this->shouldNotStop()) {
            return;
        }

        $this->isRunning = false;
        $this->api->saucer_application_quit($this->id->ptr);

        $this->listener->dispatch(new ApplicationStopped($this));
    }

    /**
     * Destructor for the Application class.
     *
     * Ensures that all resources are properly released
     * when the application is destroyed.
     */
    public function __destruct()
    {
        $this->api->saucer_application_quit($this->id->ptr);
        $this->api->saucer_application_free($this->id->ptr);
    }
}
