<?php

declare(strict_types=1);

namespace Boson\Api\Console\Driver;

use Boson\Api\Console\ConsoleApiInterface;

final readonly class VoidConsole implements ConsoleApiInterface
{
    public function detach(): void
    {
        // NO OP
    }
}
