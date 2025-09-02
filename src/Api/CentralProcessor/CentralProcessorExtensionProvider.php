<?php

declare(strict_types=1);

namespace Boson\Api\CentralProcessor;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;

/**
 * @template-implements ExtensionProviderInterface<Application>
 */
final class CentralProcessorExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [];

    public function load(IdentifiableInterface $ctx, EventListener $listener): CentralProcessorExtension
    {
        return new CentralProcessorExtension($ctx, $listener);
    }
}
