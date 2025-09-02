<?php

declare(strict_types=1);

namespace Boson\Extension\Exception;

use Psr\Container\ContainerExceptionInterface;

class ExtensionLoadingException extends ExtensionException implements
    ContainerExceptionInterface
{
    public static function becauseLoadingExceptionOccurs(\Throwable $e): self
    {
        return new self('An error occurred while loading an extension', previous: $e);
    }
}
