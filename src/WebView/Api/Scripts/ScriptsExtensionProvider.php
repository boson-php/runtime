<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\ExtensionProviderInterface;
use Boson\WebView\WebView;

/**
 * @template-implements ExtensionProviderInterface<WebView>
 */
final class ScriptsExtensionProvider implements ExtensionProviderInterface
{
    public array $dependencies = [];

    public function load(IdentifiableInterface $ctx, EventListener $listener): ScriptsExtension
    {
        return new ScriptsExtension($ctx, $listener);
    }
}
