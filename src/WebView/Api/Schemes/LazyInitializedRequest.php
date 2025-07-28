<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes;

use Boson\Component\Http\HeadersMap;
use Boson\Component\Http\Request;
use Boson\Contracts\Http\HeadersInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Internal\Saucer\LibSaucer;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Scheme
 */
final class LazyInitializedRequest implements RequestInterface
{
    /**
     * @var non-empty-uppercase-string
     */
    public string $method {
        get => $this->method ??= Request::castMethod(
            method: $this->fetchRawMethodString(),
        );
    }

    /**
     * @var non-empty-string
     */
    public string $url {
        get => $this->url ??= Request::castUrl(
            url: $this->fetchRawUriString(),
        );
    }

    public HeadersInterface $headers {
        get => $this->headers ??= HeadersMap::createFromIterable(
            headers: $this->fetchRawHeadersIterable(),
        );
    }

    public string $body {
        get => $this->body ??= $this->fetchRawBodyString();
    }

    public function __construct(
        private readonly LibSaucer $api,
        private readonly CData $ptr,
    ) {}

    private function fetchRawMethodString(): string
    {
        $method = $this->api->saucer_scheme_request_method($this->ptr);

        try {
            return \FFI::string($method);
        } finally {
            \FFI::free($method);
        }
    }

    private function fetchRawUriString(): string
    {
        $url = $this->api->saucer_scheme_request_url($this->ptr);

        try {
            return \FFI::string($url);
        } finally {
            \FFI::free($url);
        }
    }

    /**
     * @return iterable<non-empty-string, string>
     */
    private function fetchRawHeadersIterable(): iterable
    {
        $names = $this->api->new('char**');
        $values = $this->api->new('char**');
        $count = $this->api->new('size_t');

        $this->api->saucer_scheme_request_headers(
            $this->ptr,
            \FFI::addr($names),
            \FFI::addr($values),
            \FFI::addr($count),
        );

        for ($i = 0; $i < $count->cdata; ++$i) {
            /** @phpstan-ignore-next-line : PHPStan false-positive */
            $header = \FFI::string($names[$i]);

            if ($header !== '') {
                /** @phpstan-ignore-next-line : PHPStan false-positive */
                yield $header => \FFI::string($values[$i]);
            }

            $this->api->saucer_memory_free($names[$i]);
            $this->api->saucer_memory_free($values[$i]);
        }

        $this->api->saucer_memory_free($names);
        $this->api->saucer_memory_free($values);
    }

    private function fetchRawBodyString(): string
    {
        $stash = $this->api->saucer_scheme_request_content($this->ptr);

        $length = $this->api->saucer_stash_size($stash);

        try {
            if ($length <= 0) {
                return '';
            }

            $content = $this->api->saucer_stash_data($stash);

            return \FFI::string($content, $length);
        } finally {
            $this->api->saucer_stash_free($stash);
        }
    }

    public function __destruct()
    {
        $this->api->saucer_scheme_request_free($this->ptr);
    }
}
