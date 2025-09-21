<?php

declare(strict_types=1);

namespace Boson\Poller;

/**
 * @phpstan-type TaskIdType array-key
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
     * Sends an exception to the main execution thread.
     *
     * @return TaskIdType a unique identifier that can be used to cancel
     *         an exception throwing
     */
    public function throw(\Throwable $e): int|string;

    /**
     * Defer the execution of a callback.
     *
     * @param callable(TaskIdType):void $task the callback to defer
     *
     * @return TaskIdType a unique identifier that can be used to cancel
     *         the callback
     */
    public function defer(callable $task): int|string;

    /**
     * Repeatedly execute a callback.
     *
     * @param callable(TaskIdType):void $task the callback to execute
     *
     * @return TaskIdType a unique identifier that can be used to cancel
     *         the callback
     */
    public function repeat(callable $task): int|string;

    /**
     * Delay the execution of a callback.
     *
     * @param float $delay the amount of time, in seconds, to delay the execution for
     * @param callable(TaskIdType):void $task the callback to delay
     *
     * @return TaskIdType a unique identifier that can be used to
     *         cancel the callback
     */
    public function delay(float $delay, callable $task): int|string;

    /**
     * Repeatedly execute a callback with delay.
     *
     * @param float $interval the amount of time, in seconds, to interval the execution for
     * @param callable(TaskIdType):void $task the callback to execute
     *
     * @return TaskIdType a unique identifier that can be used to cancel
     *         the callback
     */
    public function timer(float $interval, callable $task): int|string;

    /**
     * Cancel a task.
     *
     * This will detach the event loop from all resources that are associated
     * to the callback. After this operation the callback is permanently
     * invalid. Calling this function MUST NOT fail, even if passed an invalid
     * identifier.
     *
     * @param TaskIdType $taskId the callback identifier
     */
    public function cancel(int|string $taskId): void;
}
