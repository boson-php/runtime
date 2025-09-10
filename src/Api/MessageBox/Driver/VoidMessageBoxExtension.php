<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox\Driver;

use Boson\Api\MessageBox\MessageBoxExtensionInterface;

final readonly class VoidMessageBoxExtension implements MessageBoxExtensionInterface
{
    public function create(string $title, string $body): never
    {
        throw new \RuntimeException('Unsupported operating system');
    }
}
