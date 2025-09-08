<?php

declare(strict_types=1);

namespace Boson\Extension\Exception;

use Psr\Container\NotFoundExceptionInterface;

final class ExtensionNotFoundException extends ExtensionException implements
    NotFoundExceptionInterface
{
    public static function becauseExtensionNotFound(string $extension, ?\Throwable $prev = null): self
    {
        $message = \sprintf('Could not load "%s" extension. '
            . 'Please make sure the extension provider is specified in '
            . 'the configuration (ApplicationCreateInfo, WindowCreateInfo or WebViewCreateInfo)', $extension);

        return new self($message, previous: $prev);
    }
}
