<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Bindings;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Attribute\DependsOn;
use Boson\Extension\Extension;
use Boson\WebView\Api\Scripts\ScriptsApiInterface;
use Boson\WebView\Api\Scripts\ScriptsExtension;
use Boson\WebView\WebView;

/**
 * @template-extends Extension<WebView>
 */
#[AvailableAs('bindings', BindingsApiInterface::class)]
#[DependsOn(ScriptsExtension::class)]
final class BindingsExtension extends Extension
{
    public function __construct(
        private readonly BindingsExtensionCreateInfo $info = new BindingsExtensionCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): BindingsApi
    {
        return new BindingsApi(
            context: $ctx,
            listener: $listener,
            info: $this->info,
            scripts: $ctx->get(ScriptsApiInterface::class),
        );
    }
}
