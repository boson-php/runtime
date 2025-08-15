<?php

declare(strict_types=1);

namespace Boson\Poller;

/**
 * Should be used to run and suspend the event loop.
 *
 * **Example**
 *
 * ```
 * $suspension = $poller->createSuspension();
 *
 * $promise->then(
 *     fn (mixed $result) => $suspension->resume($result),
 *     fn (Throwable $error) => $suspension->throw($error)
 * );
 *
 * $result = $suspension->suspend();
 * ```
 *
 * @template T of mixed = mixed
 */
interface SuspensionInterface
{
    /**
     * The value to return from the call to {@see suspend()}.
     *
     * @param T $result
     */
    public function resolve(mixed $result): void;

    /**
     * Throws the given exception from the call to {@see suspend()}.
     */
    public function reject(\Throwable $error): void;

    /**
     * Returns the value provided to {@see resume()} or throws
     * the exception provided to {@see throw()}.
     *
     * @return T
     */
    public function suspend(): mixed;
}
