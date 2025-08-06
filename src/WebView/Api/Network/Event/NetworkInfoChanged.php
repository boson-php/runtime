<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network\Event;

use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class NetworkInfoChanged extends NetworkApiEvent
{
    public function __construct(
        WebView $subject,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
