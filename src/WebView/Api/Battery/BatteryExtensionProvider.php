<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;
use Boson\WebView\Api\Bindings\BindingsExtension;
use Boson\WebView\Api\Bindings\BindingsExtensionProvider;
use Boson\WebView\Api\Data\DataExtension;
use Boson\WebView\Api\Data\DataExtensionProvider;
use Boson\WebView\Api\Scripts\ScriptsExtension;
use Boson\WebView\Api\Scripts\ScriptsExtensionProvider;
use Boson\WebView\Api\Security\SecurityExtension;
use Boson\WebView\Api\Security\SecurityExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-implements ExtensionProviderInterface<WebView>
 */
final class BatteryExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [
        DataExtensionProvider::class,
        ScriptsExtensionProvider::class,
        BindingsExtensionProvider::class,
        SecurityExtensionProvider::class,
    ];

    public function __construct(
        private readonly BatteryExtensionCreateInfo $info = new BatteryExtensionCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): BatteryExtension
    {
        return new BatteryExtension(
            context: $ctx,
            listener: $listener,
            info: $this->info,
            bindings: $ctx->get(BindingsExtension::class),
            data: $ctx->get(DataExtension::class),
            scripts: $ctx->get(ScriptsExtension::class),
            security: $ctx->get(SecurityExtension::class),
        );
    }
}
