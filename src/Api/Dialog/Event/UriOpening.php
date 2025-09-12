<?php

declare(strict_types=1);

namespace Boson\Api\Dialog\Event;

use Boson\Application;
use Boson\Shared\Marker\AsApplicationIntention;

#[AsApplicationIntention]
final class UriOpening extends DialogIntention
{
    public function __construct(
        Application $subject,
        /**
         * @var non-empty-string|\Stringable
         */
        public readonly string|\Stringable $uri,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
