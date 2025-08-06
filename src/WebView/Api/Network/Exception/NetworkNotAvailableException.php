<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network\Exception;

class NetworkNotAvailableException extends NetworkApiException
{
    public static function becauseNetworkNotAvailable(?\Throwable $previous = null): self
    {
        $message = 'The current runtime does not support getting network information';

        return new self($message, 0, $previous);
    }
}
