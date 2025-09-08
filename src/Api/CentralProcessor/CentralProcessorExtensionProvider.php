<?php

declare(strict_types=1);

namespace Boson\Api\CentralProcessor;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\ExtensionProvider;

/**
 * @template-extends ExtensionProvider<Application>
 */
#[AvailableAs(['cpu', CentralProcessorExtensionInterface::class])]
final class CentralProcessorExtensionProvider extends ExtensionProvider
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): CentralProcessorExtension
    {
        return new CentralProcessorExtension($ctx, $listener);
    }
}
