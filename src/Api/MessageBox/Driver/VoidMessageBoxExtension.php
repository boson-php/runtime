<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox\Driver;

use Boson\Api\MessageBox\MessageBoxCreateInfo;
use Boson\Api\MessageBox\MessageBoxExtensionInterface;

final readonly class VoidMessageBoxExtension implements MessageBoxExtensionInterface
{
    public function create(MessageBoxCreateInfo $info): never
    {
        throw new \RuntimeException('Unsupported operating system');
    }
}
