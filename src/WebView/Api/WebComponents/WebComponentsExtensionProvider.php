<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;
use Boson\WebView\Api\Bindings\BindingsExtension;
use Boson\WebView\Api\Bindings\BindingsExtensionProvider;
use Boson\WebView\Api\Scripts\ScriptsExtension;
use Boson\WebView\Api\Scripts\ScriptsExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-implements ExtensionProviderInterface<WebView>
 */
final class WebComponentsExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [
        BindingsExtensionProvider::class,
        ScriptsExtensionProvider::class,
    ];

    public function __construct(
        private readonly WebComponentsExtensionCreateInfo $info = new WebComponentsExtensionCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): WebComponentsExtension
    {
        return new WebComponentsExtension(
            context: $ctx,
            listener: $listener,
            info: $this->info,
            bindings: $ctx->get(BindingsExtension::class),
            scripts: $ctx->get(ScriptsExtension::class),
        );
    }
}
