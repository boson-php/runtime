<?php

declare(strict_types=1);

namespace Boson\Api\QuitSignals\Handler;

/**
 * Contract for platform-specific quit handlers that hook application termination.
 *
 * Implementations should attempt to register OS-specific event listeners or signal
 * handlers and invoke the provided callable when a quit event is triggered.
 */
interface SignalHandlerInterface
{
    /**
     * Register quit handler.
     *
     * Returns {@see true} in case of handler was successfully registered.
     *
     * @param callable():void $then
     */
    public function register(callable $then): bool;
}
