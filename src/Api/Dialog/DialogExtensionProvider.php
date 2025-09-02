<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;

/**
 * @template-implements ExtensionProviderInterface<Application>
 */
final class DialogExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [];

    public function load(IdentifiableInterface $ctx, EventListener $listener): DialogExtension
    {
        return new DialogExtension($ctx, $listener);
    }
}
