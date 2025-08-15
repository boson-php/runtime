<?php

declare(strict_types=1);

namespace Boson\Internal\Poller;

enum TaskType
{
    case Internal;
    case Periodic;
    case Queued;

    public function next(): self
    {
        return match ($this) {
            self::Internal => self::Periodic,
            self::Periodic => self::Queued,
            self::Queued => self::Internal,
        };
    }
}
