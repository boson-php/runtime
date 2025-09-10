<?php

declare(strict_types=1);

namespace Boson\Api\Autorun\Handler;

/**
 * @api
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\Autorun
 */
final readonly class NativeAutorunHandler implements AutorunHandlerInterface
{
    public function register(callable $executor): bool
    {
        \register_shutdown_function($executor);

        return true;
    }
}
