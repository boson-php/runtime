<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Security;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Extension;
use Boson\WebView\Api\Data\DataRetrieverInterface;
use Boson\WebView\WebView;

/**
 * @template-extends Extension<WebView>
 */
#[AvailableAs('security', SecurityInfoInterface::class)]
final class SecurityExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): SecurityInfo
    {
        return new SecurityInfo(
            webview: $ctx,
            listener: $listener,
            data: $ctx->get(DataRetrieverInterface::class),
        );
    }
}
