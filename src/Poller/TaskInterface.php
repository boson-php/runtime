<?php

declare(strict_types=1);

namespace Boson\Poller;

/**
 * @template TTaskId of array-key = array-key
 */
interface TaskInterface
{
    /**
     * An identifier of the task.
     *
     * @var TTaskId
     */
    public int|string $id { get; }
}
