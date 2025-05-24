<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Contracts\Http\RequestInterface;
use Illuminate\Http\Request as LaravelRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @template-covariant TRequest of LaravelRequest = LaravelRequest
 *
 * @template TResponse of SymfonyResponse = SymfonyResponse
 * @template-extends SymfonyHttpAdapter<TRequest, TResponse>
 */
readonly class LaravelHttpAdapter extends SymfonyHttpAdapter
{
    #[\Override]
    public function createRequest(RequestInterface $request): LaravelRequest
    {
        return LaravelRequest::createFromBase(
            request: parent::createRequest($request),
        );
    }
}
