<?php

declare(strict_types=1);

namespace Boson\Tests\Stub;

use Boson\Application;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Internal\SaucerWebViewEventHandler;
use Boson\Window\Internal\SaucerWindowEventHandler;
use FFI\CData;
use Revolt\EventLoop;

/**
 * @api
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Tests
 */
class TestingApplicationStub extends Application
{
    public readonly TestingSaucerStub $api;

    #[\Override]
    protected function createLibSaucer(?string $library): TestingSaucerStub
    {
        $stub = new TestingSaucerStub();

        $stub->addImplementation('cast', fn(string $t, CData $ptr) => $ptr);
        $stub->addImplementation('new', $this->createStruct(...));

        $stub->addImplementation('saucer_options_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_options_new', $args));

        $stub->addImplementation('saucer_preferences_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_preferences_new', $args));

        $stub->addImplementation('saucer_desktop_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_desktop_new', $args));

        $stub->addImplementation('saucer_application_init', fn(mixed ...$args): CData
            => $this->createStruct('saucer_application_init', $args));

        $stub->addImplementation('saucer_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_new', $args));

        $stub->addImplementation('saucer_script_new', fn(mixed ...$args): CData
            => $this->createStruct('saucer_script_new', $args));

        $stub->addImplementation('saucer_preferences_set_persistent_cookies');
        $stub->addImplementation('saucer_preferences_add_browser_flag');

        $stub->addImplementation('saucer_application_run_once');

        $stub->addImplementation('saucer_window_set_size');
        $stub->addImplementation('saucer_window_set_decorations');
        $stub->addImplementation('saucer_window_on');
        $stub->addImplementation('saucer_window_show');

        $stub->addImplementation('saucer_webview_set_context_menu');
        $stub->addImplementation('saucer_webview_set_dev_tools');
        $stub->addImplementation('saucer_webview_inject');
        $stub->addImplementation('saucer_webview_on');
        $stub->addImplementation('saucer_webview_on_message');
        $stub->addImplementation('saucer_webview_background');
        $stub->addImplementation('saucer_webview_force_dark_mode');
        $stub->addImplementation('saucer_webview_set_background');

        $stub->addImplementation('saucer_script_set_permanent');

        // cleanup

        $stub->addImplementation('saucer_free');
        $stub->addImplementation('saucer_desktop_free');
        $stub->addImplementation('saucer_preferences_free');
        $stub->addImplementation('saucer_options_free');
        $stub->addImplementation('saucer_script_free');
        $stub->addImplementation('saucer_application_free');

        $stub->addImplementation('saucer_webview_clear_scripts');
        $stub->addImplementation('saucer_webview_clear_embedded');

        $stub->addImplementation('saucer_application_quit');

        return $this->api = $stub;
    }

    #[\Override]
    #[BlockingOperation]
    public function run(): void
    {
        EventLoop::defer(function () {
            $this->quit();
        });

        parent::run();
    }

    /**
     * @param non-empty-string $type
     * @param array<array-key, mixed> $args
     */
    protected function createStruct(string $type, array $args = []): CData
    {
        return match (true) {
            $this->isWebViewEventsStruct($type) => \FFI::cdef(<<<'C'
                    typedef void* saucer_handle;
                    typedef void* saucer_navigation;
                    typedef void* saucer_icon;

                    typedef int32_t SAUCER_POLICY;
                    typedef int32_t SAUCER_STATE;
                    C)
                ->new($type),
            $this->isWindowEventsStruct($type) => \FFI::cdef(<<<'C'
                    typedef void* saucer_handle;

                    typedef int32_t SAUCER_POLICY;
                    C)
                ->new($type),
            default => \FFI::cdef()
                ->new('int64_t'),
        };
    }

    private function isWebViewEventsStruct(string $type): bool
    {
        return new \ReflectionClassConstant(SaucerWebViewEventHandler::class, 'WEBVIEW_HANDLER_STRUCT')
            ->getValue() === $type;
    }

    private function isWindowEventsStruct(string $type): bool
    {
        return new \ReflectionClassConstant(SaucerWindowEventHandler::class, 'WINDOW_HANDLER_STRUCT')
                ->getValue() === $type;
    }
}
