<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Security;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;
use Boson\WebView\Api\Data\DataExtension;
use Boson\WebView\WebView;

/**
 * @template-implements ExtensionProviderInterface<WebView>
 */
final class SecurityExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [];

    public function load(IdentifiableInterface $ctx, EventListener $listener): SecurityExtension
    {
        return new SecurityExtension(
            webview: $ctx,
            listener: $listener,
            data: $ctx->get(DataExtension::class),
        );
    }
}
