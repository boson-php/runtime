<?php

declare(strict_types=1);

namespace Boson\Api\Dialog\Event;

use Boson\Application;

abstract class ItemSelecting extends DialogIntention
{
    public function __construct(
        Application $subject,
        /**
         * @var non-empty-string|null
         */
        public readonly ?string $directory,
        /**
         * @var iterable<mixed, non-empty-string>
         */
        public iterable $filter = [],
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
