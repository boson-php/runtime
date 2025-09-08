<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\ExtensionProvider;

/**
 * @template-extends ExtensionProvider<Application>
 */
#[AvailableAs(['dialog', DialogExtensionInterface::class])]
final class DialogExtensionProvider extends ExtensionProvider
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): DialogExtension
    {
        return new DialogExtension($ctx, $listener);
    }
}
