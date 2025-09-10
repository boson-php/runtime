<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox;

use React\Promise\PromiseInterface;

interface MessageBoxExtensionInterface
{
    /**
     * @return PromiseInterface<MessageBoxButton|null>
     */
    public function create(MessageBoxCreateInfo $info): PromiseInterface;
}
