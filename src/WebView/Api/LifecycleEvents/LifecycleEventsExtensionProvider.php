<?php

declare(strict_types=1);

namespace Boson\WebView\Api\LifecycleEvents;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;
use Boson\WebView\WebView;

/**
 * @template-implements ExtensionProviderInterface<WebView>
 */
final class LifecycleEventsExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [];

    public function load(IdentifiableInterface $ctx, EventListener $listener): LifecycleEventsExtension
    {
        return new LifecycleEventsExtension($ctx, $listener);
    }
}
