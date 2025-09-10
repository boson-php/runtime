<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Attribute\DependsOn;
use Boson\Extension\ExtensionProvider;
use Boson\WebView\Api\Bindings\BindingsExtensionInterface;
use Boson\WebView\Api\Bindings\BindingsExtensionProvider;
use Boson\WebView\Api\Scripts\ScriptsExtensionInterface;
use Boson\WebView\Api\Scripts\ScriptsExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-extends ExtensionProvider<WebView>
 */
#[AvailableAs(['data', DataExtensionInterface::class])]
#[DependsOn(ScriptsExtensionProvider::class)]
#[DependsOn(BindingsExtensionProvider::class)]
final class DataExtensionProvider extends ExtensionProvider
{
    public function __construct(
        private readonly DataExtensionCreateInfo $info = new DataExtensionCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): DataExtension
    {
        return new DataExtension(
            context: $ctx,
            listener: $listener,
            info: $this->info,
            scripts: $ctx->get(ScriptsExtensionInterface::class),
            bindings: $ctx->get(BindingsExtensionInterface::class),
        );
    }
}
