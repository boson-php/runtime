<?php

declare(strict_types=1);

namespace Boson\Api\Dialog\Event;

use Boson\Application;

abstract class ItemSelected extends DialogEvent
{
    public function __construct(
        Application $subject,
        /**
         * @var non-empty-string
         */
        public readonly string $selection,
        /**
         * @var non-empty-string|null
         */
        public readonly ?string $directory,
        /**
         * @var list<non-empty-string>
         */
        public array $filter = [],
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
