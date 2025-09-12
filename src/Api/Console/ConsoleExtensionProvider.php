<?php

declare(strict_types=1);

namespace Boson\Api\Console;

use Boson\Api\Console\Driver\ConsoleDriverInterface;
use Boson\Api\Console\Driver\WindowsConsoleDriver;
use Boson\Api\Console\Event\ConsoleDetached;
use Boson\Api\Console\Event\ConsoleDetaching;
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
final class ConsoleExtensionProvider extends ExtensionProvider
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): null
    {
        $driver = $this->createDriver($ctx->get(OperatingSystemExtensionInterface::class));

        // Detach console in case of:
        // 1) Debug mode is disabled
        // 2) And application running in PHAR
        if ($driver !== null && $ctx->isDebug && $this->isRunningInPhar()) {
            $listener->dispatch($intention = new ConsoleDetaching($ctx, $driver));

            if ($intention->isCancelled) {
                return null;
            }

            $driver->detach();

            $listener->dispatch(new ConsoleDetached($ctx, $driver));
        }

        return null;
    }

    private function createDriver(OperatingSystemExtensionInterface $operatingSystem): ?ConsoleDriverInterface
    {
        if (!Runtime::isAvailable()) {
            return null;
        }

        if ($operatingSystem->family->is(Family::Windows)) {
            return new WindowsConsoleDriver();
        }

        return null;
    }

    private function isRunningInPhar(): bool
    {
        return \class_exists(\Phar::class) && \Phar::running() !== '';
    }
}
