<?php

declare(strict_types=1);

namespace Boson\Api\DetachConsole\Driver;

use Boson\Api\DetachConsole\Driver\Windows\Kernel32;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\DetachConsole
 */
final readonly class WindowsDetachConsoleDriver implements DetachConsoleDriverInterface
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
