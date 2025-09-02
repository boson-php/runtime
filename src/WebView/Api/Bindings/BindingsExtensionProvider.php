<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Bindings;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;
use Boson\WebView\Api\Scripts\ScriptsExtension;
use Boson\WebView\Api\Scripts\ScriptsExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-implements ExtensionProviderInterface<WebView>
 */
final class BindingsExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [
        ScriptsExtensionProvider::class,
    ];

    public function __construct(
        private readonly BindingsExtensionCreateInfo $info = new BindingsExtensionCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): BindingsExtension
    {
        return new BindingsExtension(
            context: $ctx,
            listener: $listener,
            info: $this->info,
            scripts: $ctx->get(ScriptsExtension::class),
        );
    }
}
