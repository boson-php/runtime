<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox;

use Boson\Api\MessageBox\Driver\VoidMessageBoxExtension;
use Boson\Api\MessageBox\Driver\WindowsMessageBoxExtension;
use Boson\Api\OperatingSystem\OperatingSystemExtensionInterface;
use Boson\Api\OperatingSystem\OperatingSystemExtensionProvider;
use Boson\Application;
use Boson\Component\OsInfo\Family;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Attribute\DependsOn;
use Boson\Extension\ExtensionProvider;

/**
 * @template-extends ExtensionProvider<Application>
 */
#[AvailableAs(['msgbox', MessageBoxExtensionInterface::class])]
#[DependsOn(OperatingSystemExtensionProvider::class)]
final class MessageBoxExtensionProvider extends ExtensionProvider
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): MessageBoxExtensionInterface
    {
        $os = $ctx->get(OperatingSystemExtensionInterface::class);

        return match (true) {
            $os->family->is(Family::Windows) => new WindowsMessageBoxExtension(),
            default => new VoidMessageBoxExtension(),
        };
    }
}
