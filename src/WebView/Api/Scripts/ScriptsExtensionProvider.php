<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\ExtensionProvider;
use Boson\WebView\WebView;

/**
 * @template-extends ExtensionProvider<WebView>
 */
#[AvailableAs(['scripts', ScriptsExtensionInterface::class])]
final class ScriptsExtensionProvider extends ExtensionProvider
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): ScriptsExtension
    {
        return new ScriptsExtension($ctx, $listener);
    }
}
