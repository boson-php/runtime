<?php

declare(strict_types=1);

namespace Boson\Api\Console\Driver;

use Boson\Api\Console\Driver\Windows\Kernel32;

/**
 * @api
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\Console
 */
final readonly class WindowsConsoleDriver implements ConsoleDriverInterface
{
    public function __construct(
        private Kernel32 $kernel32 = new Kernel32(),
    ) {}

    public function detach(): void
    {
        $this->kernel32->FreeConsole();
    }
}

__halt_compiler();

bool FreeConsole(void);
