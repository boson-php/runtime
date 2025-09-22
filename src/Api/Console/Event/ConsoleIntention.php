<?php

declare(strict_types=1);

namespace Boson\Api\Console\Event;

use Boson\Api\Console\ConsoleApiInterface;
use Boson\Application;
use Boson\Event\ApplicationApiIntention;

abstract class ConsoleIntention extends ApplicationApiIntention
{
    public function __construct(
        Application $subject,
        public readonly ConsoleApiInterface $driver,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
