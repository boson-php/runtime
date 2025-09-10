<?php

declare(strict_types=1);

namespace Boson\Api\DetachConsole\Driver;

interface DetachConsoleDriverInterface
{
    public function detach(): void;
}
