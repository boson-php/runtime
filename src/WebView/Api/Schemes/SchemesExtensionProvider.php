<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\ExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-extends ExtensionProvider<WebView>
 */
#[AvailableAs(['schemes', SchemesExtensionInterface::class])]
final class SchemesExtensionProvider extends ExtensionProvider
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): SchemesExtension
    {
        return new SchemesExtension(
            context: $ctx,
            listener: $listener,
        );
    }
}
