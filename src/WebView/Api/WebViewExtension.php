<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Api\Extension;
use Boson\Dispatcher\EventListener;
use Boson\WebView\WebView;

/**
 * @template-extends Extension<WebView>
 */
abstract class WebViewExtension extends Extension
{
    public function __construct(WebView $context, EventListener $listener)
    {
        parent::__construct($context, $listener);
    }
}
