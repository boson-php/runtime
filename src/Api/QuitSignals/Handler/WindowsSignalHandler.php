<?php

declare(strict_types=1);

namespace Boson\Api\QuitSignals\Handler;

/**
 * Windows-specific quit handler using CTRL handler API.
 *
 * Registers a console control handler to trigger application quit on
 * `ctrl+c` or console close events when supported.
 */
final class WindowsSignalHandler implements SignalHandlerInterface
{
    /**
     * @param callable():void $then quit routine to be invoked on quit signal
     */
    public function register(callable $then): bool
    {
        $isSupported = \function_exists('\\sapi_windows_set_ctrl_handler');

        if ($isSupported) {
            $this->listenHandler($then);
        }

        return $isSupported;
    }

    /**
     * @param callable():void $then
     */
    private function listenHandler(callable $then): void
    {
        \sapi_windows_set_ctrl_handler($then(...));
    }
}
