<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Application;

use Boson\ApplicationCreateInfo;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/runtime')]
final class ApplicationConfigurationTest extends ApplicationTestCase
{
    private function isPHPDebugModeEnabled(): bool
    {
        $debug = false;

        assert($debug = true);

        return $debug;
    }

    public function testDebugValueIsEqualToPHPGlobalSettings(): void
    {
        $app = $this->createApplication();

        self::assertSame($this->isPHPDebugModeEnabled(), $app->isDebug);
    }

    public function testEnableDebug(): void
    {
        $app = $this->createApplication(new ApplicationCreateInfo(
            debug: true,
        ));

        $devTools = null;
        $contextMenu = null;

        $app->api->onMethodCall('saucer_webview_set_dev_tools', function ($_, bool $enabled) use (&$devTools) {
            $devTools = $enabled;
        });

        $app->api->onMethodCall('saucer_webview_set_context_menu', function ($_, bool $enabled) use (&$contextMenu) {
            $contextMenu = $enabled;
        });

        $app->run();

        self::assertTrue($app->isDebug);
        self::assertTrue($devTools);
        self::assertTrue($contextMenu);
    }

    public function testDisableDebug(): void
    {
        $app = $this->createApplication(new ApplicationCreateInfo(
            debug: false,
        ));

        $devTools = null;
        $contextMenu = null;

        $app->api->onMethodCall('saucer_webview_set_dev_tools', function ($_, bool $enabled) use (&$devTools) {
            $devTools = $enabled;
        });

        $app->api->onMethodCall('saucer_webview_set_context_menu', function ($_, bool $enabled) use (&$contextMenu) {
            $contextMenu = $enabled;
        });

        $app->run();

        self::assertFalse($app->isDebug);
        self::assertFalse($devTools);
        self::assertFalse($contextMenu);
    }
}
