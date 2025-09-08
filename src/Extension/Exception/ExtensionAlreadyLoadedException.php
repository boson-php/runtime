<?php

declare(strict_types=1);

namespace Boson\Extension\Exception;

use Psr\Container\NotFoundExceptionInterface;

final class ExtensionAlreadyLoadedException extends ExtensionException implements
    NotFoundExceptionInterface
{
    public static function becauseExtensionAlreadyLoaded(string $extension, ?\Throwable $prev = null): self
    {
        $message = \sprintf('Extension %s has been already loaded', $extension);

        return new self($message, previous: $prev);
    }
}
