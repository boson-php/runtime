<?php

declare(strict_types=1);

namespace Boson\Api\Dialog\Event;

use Boson\Application;
use Boson\Shared\Marker\AsApplicationEvent;

#[AsApplicationEvent]
final class UriOpened extends DialogEvent
{
    public function __construct(
        Application $subject,
        /**
         * @var non-empty-string
         */
        public readonly string $uri,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
