<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network\Exception;

class NetworkNotReadyException extends NetworkApiException
{
    public static function becauseNetworkNotReady(?\Throwable $previous = null): self
    {
        $message = 'Obtaining network information is only available after the document is ready';

        return new self($message, 0, $previous);
    }
}
