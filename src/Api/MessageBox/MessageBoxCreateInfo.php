<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox;

final readonly class MessageBoxCreateInfo
{
    public function __construct(
        public string $title,
        public string $text,
        public bool $cancel = false,
        public ?MessageBoxIcon $icon = null,
    ) {}
}
