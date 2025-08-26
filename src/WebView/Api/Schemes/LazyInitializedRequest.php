<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes;

use Boson\Component\Http\Request;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Contracts\Http\Component\HeadersInterface;
use Boson\Contracts\Http\Component\MethodInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Uri\UriInterface;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Scheme
 */
final class LazyInitializedRequest implements RequestInterface
{
    public MethodInterface $method {
        get => $this->method ??= Request::castMethod($this->fetchRawMethodString());
    }

    public UriInterface $url {
        get => $this->url ??= Request::castUrl($this->fetchRawUriString());
    }

    public HeadersInterface $headers {
        get => $this->headers ??= Request::castHeaders($this->fetchRawHeadersIterable());
    }

    public string $body {
        get => $this->body ??= Request::castBody($this->fetchRawBodyString());
    }

    public function __construct(
        private readonly SaucerInterface $api,
        private readonly CData $ptr,
    ) {}

    /**
     * @return non-empty-string
     */
    private function fetchRawMethodString(): string
    {
        $method = $this->api->saucer_scheme_request_method($this->ptr);

        try {
            $scalar = \FFI::string($method);

            if ($scalar === '') {
                /** @var non-empty-uppercase-string */
                return (string) Request::DEFAULT_METHOD;
            }

            return $scalar;
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
            /** @var CData $name */
            $name = $names[$i];

            /** @var CData $value */
            $value = $values[$i];

            $header = \FFI::string($name);

            if ($header !== '') {
                yield $header => \FFI::string($value);
            }

            $this->api->saucer_memory_free($name);
            $this->api->saucer_memory_free($value);
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
