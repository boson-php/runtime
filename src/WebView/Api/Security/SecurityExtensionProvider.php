<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Security;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\ExtensionProvider;
use Boson\WebView\Api\Data\DataExtension;
use Boson\WebView\WebView;

/**
 * @template-extends ExtensionProvider<WebView>
 */
#[AvailableAs(['security', SecurityExtensionInterface::class])]
final class SecurityExtensionProvider extends ExtensionProvider
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): SecurityExtension
    {
        return new SecurityExtension(
            webview: $ctx,
            listener: $listener,
            data: $ctx->get(DataExtension::class),
        );
    }
}
