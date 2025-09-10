<?php

declare(strict_types=1);

namespace Boson\Api\QuitHandler\Handler;

/**
 * POSIX-specific quit handler using PCNTL signals.
 *
 * Registers handlers for common termination signals when PCNTL is available
 * and enabled in the runtime.
 */
final class PcntlQuitHandler implements QuitHandlerInterface
{
    /**
     * @param callable():void $then quit routine to be invoked on quit signal
     */
    public function register(callable $then): bool
    {
        $isSupported = \extension_loaded('pcntl')
            // PCNTL functions may be disabled "for security reasons"
            // @link https://stackoverflow.com/questions/16262854/pcntl-not-working-on-ubuntu-for-security-reasons
            && \function_exists('\\pcntl_async_signals')
            && \function_exists('\\pcntl_signal');

        if ($isSupported) {
            $this->listenSignals($then);
        }

        return $isSupported;
    }

    /**
     * @param callable():void $then
     */
    private function listenSignals(callable $then): void
    {
        \pcntl_async_signals(true);

        foreach ([\SIGINT, \SIGQUIT, \SIGTERM] as $signal) {
            \pcntl_signal($signal, $then(...));
        }
    }
}
