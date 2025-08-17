<?php

declare(strict_types=1);

namespace Boson\Api;

/**
 * @seal-methods
 * @seal-properties
 */
interface SaucerInterface
{
    /**
     * @param non-empty-string $method
     * @param array<non-empty-string|int<0, max>, mixed> $args
     */
    public function __call(string $method, array $args): mixed;
}
