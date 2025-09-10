<?php

declare(strict_types=1);

namespace Boson\Api\DetachConsole;

use Boson\Api\DetachConsole\Driver\DetachConsoleDriverInterface;
use Boson\Api\DetachConsole\Driver\WindowsDetachConsoleDriver;
use Boson\Api\OperatingSystem\OperatingSystemExtensionInterface;
use Boson\Api\OperatingSystem\OperatingSystemExtensionProvider;
use Boson\Application;
use Boson\Component\OsInfo\Family;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\DependsOn;
use Boson\Extension\ExtensionProvider;
use FFI\Env\Runtime;

/**
 * @template-extends ExtensionProvider<Application>
 */
#[DependsOn(OperatingSystemExtensionProvider::class)]
final class DetachConsoleExtensionProvider extends ExtensionProvider
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): null
    {
        $driver = $this->createDriver($ctx->get(OperatingSystemExtensionInterface::class));

        // Detach console in case of:
        // 1) Debug mode is disabled
        // 2) And application running in PHAR
        if ($ctx->isDebug && $this->isRunningInPhar()) {
            $driver?->detach();
        }

        return null;
    }

    private function createDriver(OperatingSystemExtensionInterface $operatingSystem): ?DetachConsoleDriverInterface
    {
        if (!Runtime::isAvailable()) {
            return null;
        }

        if ($operatingSystem->family->is(Family::Windows)) {
            return new WindowsDetachConsoleDriver();
        }

        return null;
    }

    private function isRunningInPhar(): bool
    {
        return \class_exists(\Phar::class) && \Phar::running() !== '';
    }
}
