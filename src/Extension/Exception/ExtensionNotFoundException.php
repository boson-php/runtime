<?php

declare(strict_types=1);

namespace Boson\Extension\Exception;

use Psr\Container\NotFoundExceptionInterface;

final class ExtensionNotFoundException extends ExtensionException implements
    NotFoundExceptionInterface
{
    public static function becauseExceptionNotFound(string $extension, ?\Throwable $prev = null): self
    {
        $message = \sprintf('Could not load extension: %s', $extension);

        return new self($message, previous: $prev);
    }
}
