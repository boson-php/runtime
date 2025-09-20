<?php

declare(strict_types=1);

namespace Boson\Poller;

/**
 * @template TTaskId of array-key = array-key
 */
interface PollerInterface
{
    /**
     * Poll next application loop event.
     */
    public function next(): void;

    /**
     * Returns an object used to suspend and resume
     * execution of the current process.
     */
    public function createSuspension(): SuspensionInterface;

    /**
     * Defer the execution of a callback.
     *
     * @param callable(TaskInterface<TTaskId>):void $task the callback to defer
     *
     * @return TaskInterface<TTaskId>
     */
    public function defer(callable $task): TaskInterface;

    /**
     * Repeatedly execute a callback.
     *
     * @param callable(TaskInterface<TTaskId>):void $task the callback to execute
     *
     * @return TaskInterface<TTaskId>
     */
    public function repeat(callable $task): TaskInterface;

    /**
     * Delay the execution of a callback.
     *
     * @param float $delay the amount of time, in seconds, to delay the execution for
     * @param callable(TaskInterface<TTaskId>):void $task the callback to delay
     *
     * @return TaskInterface<TTaskId>
     */
    public function delay(float $delay, callable $task): TaskInterface;

    /**
     * Cancel a task.
     *
     * This will detach the event loop from all resources that are associated
     * to the callback. After this operation the callback is permanently
     * invalid.
     *
     * @param TaskInterface<TTaskId> $task
     */
    public function cancel(TaskInterface $task): void;
}
