<?php

declare(strict_types=1);

namespace Boson\Api\Console\Event;

use Boson\Api\Console\Driver\ConsoleDriverInterface;
use Boson\Application;
use Boson\Event\ApplicationApiEvent;

abstract class ConsoleEvent extends ApplicationApiEvent
{
    public function __construct(
        Application $subject,
        public readonly ConsoleDriverInterface $driver,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
