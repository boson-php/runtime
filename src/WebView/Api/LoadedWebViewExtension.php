<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Internal\StructPointerId;
use Boson\WebView\WebView;
use Boson\Window\Api\LoadedWindowExtension;

/**
 * @template TContext of IdentifiableInterface<StructPointerId> = WebView
 *
 * @template-extends LoadedWindowExtension<TContext>
 */
abstract class LoadedWebViewExtension extends LoadedWindowExtension
{
    /**
     * Gets reference to the context's ID
     */
    protected StructPointerId $id {
        #[\Override]
        get => $this->webview->id;
    }

    public function __construct(
        protected readonly WebView $webview,
        EventListener $listener,
    ) {
        parent::__construct($webview->window, $listener);
    }
}
