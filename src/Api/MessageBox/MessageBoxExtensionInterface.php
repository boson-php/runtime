<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox;

interface MessageBoxExtensionInterface
{
    public function create(MessageBoxCreateInfo $info): ?MessageBoxButton;
}
