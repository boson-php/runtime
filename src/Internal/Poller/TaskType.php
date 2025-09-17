<?php

declare(strict_types=1);

namespace Boson\Internal\Poller;

enum TaskType
{
    case Internal;
    case Queued;
    case Periodic;

    public const self DEFAULT = self::Queued;

    public function next(): self
    {
        return match ($this) {
            self::Internal => self::Queued,
            self::Queued => self::Periodic,
            self::Periodic => self::Internal,
        };
    }
}
