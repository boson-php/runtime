<?php

declare(strict_types=1);

namespace Boson\Api\Console\Driver;

interface ConsoleDriverInterface
{
    public function detach(): void;
}
