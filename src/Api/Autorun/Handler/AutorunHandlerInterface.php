<?php

declare(strict_types=1);

namespace Boson\Api\Autorun\Handler;

interface AutorunHandlerInterface
{
    /**
     * Register autorun handler.
     *
     * Returns {@see true} in case of handler was successfully registered.
     *
     * @param callable():void $executor
     */
    public function register(callable $executor): bool;
}
