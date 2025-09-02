<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;
use Boson\WebView\Api\Bindings\BindingsExtension;
use Boson\WebView\Api\Bindings\BindingsExtensionProvider;
use Boson\WebView\Api\Data\DataExtension;
use Boson\WebView\Api\Data\DataExtensionProvider;
use Boson\WebView\Api\Scripts\ScriptsExtension;
use Boson\WebView\Api\Scripts\ScriptsExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-implements ExtensionProviderInterface<WebView>
 */
final class NetworkExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [
        ScriptsExtensionProvider::class,
        BindingsExtensionProvider::class,
        DataExtensionProvider::class,
    ];

    public function __construct(
        public readonly NetworkExtensionCreateInfo $info = new NetworkExtensionCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): NetworkExtension
    {
        return new NetworkExtension(
            context: $ctx,
            listener: $listener,
            info: $this->info,
            bindings: $ctx->get(BindingsExtension::class),
            scripts: $ctx->get(ScriptsExtension::class),
            data: $ctx->get(DataExtension::class),
        );
    }
}
