<?php

declare(strict_types=1);

namespace Boson\WebView\Event;

use Boson\Shared\Marker\AsWebViewEvent;

#[AsWebViewEvent]
final class WebViewFaviconChanged extends WebViewEvent {}
