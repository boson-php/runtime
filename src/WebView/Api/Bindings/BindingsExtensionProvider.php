<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Bindings;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Attribute\DependsOn;
use Boson\Extension\ExtensionProvider;
use Boson\WebView\Api\Scripts\ScriptsExtensionInterface;
use Boson\WebView\Api\Scripts\ScriptsExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-extends ExtensionProvider<WebView>
 */
#[AvailableAs(['bindings', BindingsExtensionInterface::class])]
#[DependsOn(ScriptsExtensionProvider::class)]
final class BindingsExtensionProvider extends ExtensionProvider
{
    public function __construct(
        private readonly BindingsExtensionCreateInfo $info = new BindingsExtensionCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): BindingsExtension
    {
        return new BindingsExtension(
            context: $ctx,
            listener: $listener,
            info: $this->info,
            scripts: $ctx->get(ScriptsExtensionInterface::class),
        );
    }
}
