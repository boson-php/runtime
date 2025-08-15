<?php

declare(strict_types=1);

namespace Boson\Poller;

interface PollerInterface
{
    /**
     * Poll next application loop event.
     */
    public function next(): void;

    /**
     * Defer the execution of a callback.
     *
     * @template TArgTaskId of array-key
     *
     * @param callable(TArgTaskId):void $task The callback to defer.
     *
     * @return TArgTaskId An unique identifier that can be used to cancel
     *         the callback.
     */
    public function defer(callable $task): int|string;

    /**
     * Repeatedly execute a callback.
     *
     * @template TArgTaskId of array-key
     *
     * @param callable(TArgTaskId):void $task The callback to execute.
     *
     * @return TArgTaskId An unique identifier that can be used to cancel
     *         the callback.
     */
    public function repeat(callable $task): int|string;

    /**
     * Delay the execution of a callback.
     *
     * @template TArgTaskId of array-key
     *
     * @param float $delay The amount of time, in seconds, to delay the execution for.
     * @param callable(TArgTaskId):void $task The callback to delay.
     *
     * @return TArgTaskId A unique identifier that can be used to
     *         cancel the callback.
     */
    public function delay(float $delay, callable $task): int|string;

    /**
     * Cancel a task.
     *
     * This will detach the event loop from all resources that are associated
     * to the callback. After this operation the callback is permanently
     * invalid. Calling this function MUST NOT fail, even if passed an invalid
     * identifier.
     *
     * @param array-key $taskId The callback identifier.
     */
    public function cancel(int|string $taskId): void;
}
