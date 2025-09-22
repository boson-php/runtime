<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Attribute\DependsOn;
use Boson\Extension\Extension;
use Boson\WebView\Api\Bindings\BindingsApiInterface;
use Boson\WebView\Api\Bindings\BindingsExtension;
use Boson\WebView\Api\Scripts\ScriptsApiInterface;
use Boson\WebView\Api\Scripts\ScriptsExtension;
use Boson\WebView\WebView;

/**
 * @template-extends Extension<WebView>
 */
#[AvailableAs('data', DataRetrieverInterface::class)]
#[DependsOn(ScriptsExtension::class)]
#[DependsOn(BindingsExtension::class)]
final class DataExtension extends Extension
{
    public function __construct(
        private readonly DataExtensionCreateInfo $info = new DataExtensionCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): DataRetriever
    {
        return new DataRetriever(
            context: $ctx,
            listener: $listener,
            info: $this->info,
            scripts: $ctx->get(ScriptsApiInterface::class),
            bindings: $ctx->get(BindingsApiInterface::class),
        );
    }
}
