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
    /**
     * @var non-empty-string
     */
    private const string BOSON_CLIENT_API = __DIR__ . '/../../../../resources/dist/main.js.php';

    public function load(IdentifiableInterface $ctx, EventListener $listener): ScriptsExtension
    {
        $extension = new ScriptsExtension($ctx, $listener);
        $extension->preload((string) @\file_get_contents(self::BOSON_CLIENT_API), true);

        return $extension;
    }
}
