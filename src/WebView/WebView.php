<?php

declare(strict_types=1);

namespace Boson\WebView;

use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Dispatcher\EventListenerInterface;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Dispatcher\EventListenerProviderInterface;
use Boson\Exception\BosonException;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\Battery\WebViewBattery;
use Boson\WebView\Api\BatteryApiInterface;
use Boson\WebView\Api\Bindings\Exception\FunctionAlreadyDefinedException;
use Boson\WebView\Api\Bindings\WebViewBindingsMap;
use Boson\WebView\Api\BindingsApiInterface;
use Boson\WebView\Api\Data\WebViewData;
use Boson\WebView\Api\DataApiInterface;
use Boson\WebView\Api\Schemes\WebViewSchemeHandler;
use Boson\WebView\Api\SchemesApiInterface;
use Boson\WebView\Api\Scripts\WebViewScriptsSet;
use Boson\WebView\Api\ScriptsApiInterface;
use Boson\WebView\Api\Security\WebViewSecurity;
use Boson\WebView\Api\SecurityApiInterface;
use Boson\WebView\Api\WebComponents\Exception\ComponentAlreadyDefinedException;
use Boson\WebView\Api\WebComponents\Exception\WebComponentsApiException;
use Boson\WebView\Api\WebComponents\WebViewWebComponents;
use Boson\WebView\Api\WebComponentsApiInterface;
use Boson\WebView\Api\WebViewExtension;
use Boson\WebView\Internal\WebViewEventHandler;
use Boson\Window\Window;
use FFI\CData;
use JetBrains\PhpStorm\Language;

final class WebView implements EventListenerProviderInterface
{
    use EventListenerProvider;

    /**
     * @var non-empty-string
     */
    private const string PRELOADED_SCRIPTS_DIRECTORY = __DIR__ . '/../../resources/dist';

    /**
     * Gets access to the listener of the webview events
     * and intention subscriptions.
     */
    public readonly EventListenerInterface $events;

    /**
     * WebView-aware event dispatcher.
     */
    private readonly EventDispatcherInterface $dispatcher;

    /**
     * Gets access to the Scripts API of the webview.
     *
     * Provides the ability to register a JavaScript code
     * in the webview.
     */
    public readonly ScriptsApiInterface $scripts;

    /**
     * Gets access to the Bindings API of the webview.
     *
     * Provides the ability to register PHP functions
     * in the webview.
     */
    public readonly BindingsApiInterface $bindings;

    /**
     * Gets access to the Data API of the webview.
     *
     * Provides the ability to receive variant data from
     * the current document.
     */
    public readonly DataApiInterface $data;

    /**
     * Gets access to the Security API of the webview.
     */
    public readonly SecurityApiInterface $security;

    /**
     * Gets access to the Web Components API of the webview.
     */
    public readonly WebComponentsApiInterface $components;

    /**
     * Gets access to the Battery API of the webview.
     */
    public readonly BatteryApiInterface $battery;

    /**
     * Gets access to the Schemes API of the webview.
     */
    public readonly SchemesApiInterface $schemes;

    /**
     * Contains webview URI instance.
     */
    public string $url {
        /**
         * Gets current webview URI instance.
         *
         * ```
         * echo $webview->url; // http://example.com
         * ```
         */
        get {
            $result = $this->api->saucer_webview_url($this->ptr);

            try {
                return \FFI::string($result);
            } finally {
                \FFI::free($result);
            }
        }
        /**
         * Updates URI of the webview.
         *
         * This can also be considered as navigation to a specific web page.
         *
         * ```
         * $webview->url = 'http://example.com';
         * ```
         */
        set(\Stringable|string $value) {
            $this->api->saucer_webview_set_url($this->ptr, (string) $value);
        }
    }

    /**
     * Load HTML content into the WebView.
     */
    public string $html {
        set(#[Language('HTML')] string|\Stringable $html) {
            $base64 = \base64_encode((string) $html);

            $this->url = \sprintf('data:text/html;base64,%s', $base64);
        }
    }

    /**
     * Gets webview status.
     */
    public private(set) WebViewState $state = WebViewState::Loading;

    /**
     * Internal window's webview pointer (handle).
     */
    private readonly CData $ptr;

    /**
     * Contains an internal bridge between {@see LibSaucer} events system
     * and the PSR {@see WebView::$events} dispatcher.
     *
     * @phpstan-ignore property.onlyWritten
     */
    private readonly WebViewEventHandler $internalWebViewEventHandler;

    /**
     * @internal Please do not use the constructor directly. There is a
     *           corresponding {@see WindowFactoryInterface::create()} method
     *           for creating new windows with single webview child instance,
     *           which ensures safe creation.
     *           ```
     *           $app = new Application();
     *
     *           // Should be used instead of calling the constructor
     *           $window = $app->windows->create();
     *
     *           // Access to webview child instance
     *           $webview = $window->webview;
     *           ```
     */
    public function __construct(
        /**
         * Contains shared WebView API library.
         */
        private readonly LibSaucer $api,
        /**
         * Gets parent application window instance to which
         * this webview instance belongs.
         */
        public readonly Window $window,
        /**
         * Gets information DTO about the webview with which it was created.
         */
        public readonly WebViewCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        $this->events = $this->dispatcher = new DelegateEventListener($dispatcher);
        // The WebView handle pointer is the same as the Window pointer.
        $this->ptr = $this->window->id->ptr;

        $this->scripts = $this->createWebViewExtension(WebViewScriptsSet::class);
        $this->bindings = $this->createWebViewExtension(WebViewBindingsMap::class);
        $this->data = $this->createWebViewExtension(WebViewData::class);
        $this->security = $this->createWebViewExtension(WebViewSecurity::class);
        $this->components = $this->createWebViewExtension(WebViewWebComponents::class);
        $this->battery = $this->createWebViewExtension(WebViewBattery::class);
        $this->schemes = $this->createWebViewExtension(WebViewSchemeHandler::class);

        $this->internalWebViewEventHandler = $this->createWebViewEventHandler();

        $this->loadRuntimeScripts();
    }

    /**
     * @template TArgApiProvider of WebViewExtension
     *
     * @param class-string<TArgApiProvider> $class
     *
     * @return TArgApiProvider
     */
    private function createWebViewExtension(string $class): WebViewExtension
    {
        return new $class(
            api: $this->api,
            context: $this,
            listener: $this->events,
            dispatcher: $this->dispatcher,
        );
    }

    private function createWebViewEventHandler(): WebViewEventHandler
    {
        return new WebViewEventHandler(
            api: $this->api,
            webview: $this,
            dispatcher: $this->events,
            state: $this->state,
        );
    }

    /**
     * Loads predefined scripts list
     */
    private function loadRuntimeScripts(): void
    {
        $filesystem = new \FilesystemIterator(self::PRELOADED_SCRIPTS_DIRECTORY);

        foreach ($filesystem as $script) {
            if (!$script instanceof \SplFileInfo || !$script->isFile()) {
                continue;
            }

            $code = @\file_get_contents($script->getPathname());

            if ($code === false) {
                throw new BosonException(\sprintf('Unable to read %s', $script->getPathname()));
            }

            $this->scripts->preload($code, true);
        }
    }

    /**
     * Binds a PHP callback to a new global JavaScript function.
     *
     * Note: This is facade method of the {@see WebViewBindingsMap::bind()},
     *       that provides by the {@see $bindings} field. This means that
     *       calling `$webview->functions->bind(...)` should have the same effect.
     *
     * @api
     *
     * @param non-empty-string $function
     *
     * @throws FunctionAlreadyDefinedException in case of function binding error
     *
     * @uses BindingsApiInterface::bind() WebView Functions API
     */
    public function bind(string $function, \Closure $callback): void
    {
        $this->bindings->bind($function, $callback);
    }

    /**
     * Evaluates arbitrary JavaScript code.
     *
     * Note: This is facade method of the {@see WebViewScriptsSet::eval()},
     *       that provides by the {@see $scripts} field. This means that
     *       calling `$webview->scripts->eval(...)` should have the same effect.
     *
     * @api
     *
     * @uses ScriptsApiInterface::eval() WebView Scripts API
     *
     * @param string $code A JavaScript code for execution
     */
    public function eval(#[Language('JavaScript')] string $code): void
    {
        $this->scripts->eval($code);
    }

    /**
     * Requests arbitrary data from webview using JavaScript code.
     *
     * Note: This is facade method of the {@see WebViewData::get()},
     *       that provides by the {@see $data} field. This means that
     *       calling `$webview->requests->send(...)` should have the same effect.
     *
     * @api
     *
     * @param string $code A JavaScript code for execution
     *
     * @uses DataApiInterface::get() WebView Requests API
     */
    #[BlockingOperation]
    public function get(#[Language('JavaScript')] string $code, ?float $timeout = null): mixed
    {
        return $this->data->get($code, $timeout);
    }

    /**
     * Registers a new component with the given tag name and component class.
     *
     * @api
     *
     * @param non-empty-string $name The component name (tag)
     * @param class-string $component The fully qualified class name of the component
     *
     * @throws ComponentAlreadyDefinedException if a component with the given name is already registered
     * @throws WebComponentsApiException if any other registration error occurs
     *
     * @uses WebComponentsApiInterface::add() WebView Web Components API
     */
    public function defineComponent(string $name, string $component): void
    {
        $this->components->add($name, $component);
    }

    /**
     * Go forward using current history.
     *
     * @api
     */
    public function forward(): void
    {
        $this->api->saucer_webview_forward($this->ptr);
    }

    /**
     * Go back using current history.
     *
     * @api
     */
    public function back(): void
    {
        $this->api->saucer_webview_back($this->ptr);
    }

    /**
     * Reload current layout.
     *
     * @api
     */
    public function reload(): void
    {
        $this->api->saucer_webview_reload($this->ptr);
    }
}
