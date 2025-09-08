<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Attribute\DependsOn;
use Boson\Extension\ExtensionProvider;
use Boson\WebView\Api\Bindings\BindingsExtension;
use Boson\WebView\Api\Bindings\BindingsExtensionProvider;
use Boson\WebView\Api\Data\DataExtension;
use Boson\WebView\Api\Data\DataExtensionProvider;
use Boson\WebView\Api\Scripts\ScriptsExtension;
use Boson\WebView\Api\Scripts\ScriptsExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-extends ExtensionProvider<WebView>
 */
#[AvailableAs(['network', NetworkExtensionInterface::class])]
#[DependsOn(ScriptsExtensionProvider::class)]
#[DependsOn(BindingsExtensionProvider::class)]
#[DependsOn(DataExtensionProvider::class)]
final class NetworkExtensionProvider extends ExtensionProvider
{
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
