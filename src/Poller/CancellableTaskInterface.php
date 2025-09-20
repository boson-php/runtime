<?php

declare(strict_types=1);

namespace Boson\Poller;

/**
 * @template TTaskId of array-key = array-key
 *
 * @template-extends TaskInterface<TTaskId>
 */
interface CancellableTaskInterface extends TaskInterface
{
    /**
     * Returns {@see true} in case of the task is
     * cancelled, {@see false} otherwise.
     */
    public bool $isCancelled { get; }

    /**
     * Cancel the task.
     */
    public function cancel(): void;
}
