<?php

declare(strict_types=1);

namespace Boson\Bridge\Static;

use Boson\Bridge\Static\Mime\ExtensionFileDetector;
use Boson\Bridge\Static\Mime\FileDetectorInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\Http\Response;

final readonly class FilesystemStaticAdapter implements StaticAdapterInterface
{
    /**
     * @var list<non-empty-lowercase-string>
     */
    private const array KNOWN_TEXT_MIME_TYPES = [
        'application/xhtml+xml',
        'application/javascript',
        'application/json',
    ];

    /**
     * @var non-empty-lowercase-string
     */
    private const string DEFAULT_CHARSET = 'utf-8';

    /**
     * @var non-empty-lowercase-string
     */
    private const string DEFAULT_CONTENT_TYPE = 'text/html';

    /**
     * @var list<non-empty-string>
     */
    private array $root;

    /**
     * @param iterable<mixed, non-empty-string>|non-empty-string $root
     *        List of root (public directories) for files lookup
     */
    public function __construct(
        iterable|string $root = [],
        /**
         * Contains default mime type for undetectable files.
         *
         * @var non-empty-string
         */
        private string $defaultMimeType = self::DEFAULT_CONTENT_TYPE,
        /**
         * Contains default charset for text files.
         *
         * @var non-empty-string
         */
        private string $defaultCharset = self::DEFAULT_CHARSET,
        /**
         * Contains mime type detector.
         */
        private FileDetectorInterface $mimeDetector = new ExtensionFileDetector(),
    ) {
        if (\is_string($root)) {
            $root = [$root];
        }

        $this->root = \iterator_to_array(
            iterator: \is_iterable($root) ? $root : [$root],
            preserve_keys: false,
        );
    }

    /**
     * @return non-empty-string|null
     */
    private function findPathnameForExistingFile(RequestInterface $request): ?string
    {
        $path = \parse_url($request->url, \PHP_URL_PATH);

        if (!\is_string($path) || $path === '') {
            return null;
        }

        foreach ($this->root as $root) {
            $pathname = $root . '/' . $path;

            if (!\is_file($pathname) || !\is_readable($pathname)) {
                continue;
            }

            return $pathname;
        }

        return null;
    }

    public function lookup(RequestInterface $request): ?ResponseInterface
    {
        $pathname = $this->findPathnameForExistingFile($request);

        if ($pathname === null) {
            return null;
        }

        $contentType = $this->getContentType($pathname);

        return new Response(
            body: (string) \file_get_contents($pathname),
            headers: ['content-type' => $contentType],
        );
    }

    /**
     * @param non-empty-string $pathname
     *
     * @return non-empty-string
     */
    private function getContentType(string $pathname): string
    {
        /** @var non-empty-string $mimeType */
        $mimeType = $this->mimeDetector->detectByFile($pathname)
            ?? $this->defaultMimeType;

        // Returns mime type with charset in case of file supports charset
        if (($charset = $this->getContentTypeCharset($mimeType)) !== null) {
            return $mimeType . '; ' . $charset;
        }

        return $mimeType;
    }

    /**
     * Returns default charset segment for content-type header value
     * by passed mime type value.
     *
     * @param non-empty-string $mimeType
     *
     * @return non-empty-string|null
     */
    private function getContentTypeCharset(string $mimeType): ?string
    {
        // Skip in case of charset already has been defined
        if (\str_contains($mimeType, 'charset=')) {
            return null;
        }

        if ($this->supportsContentType($mimeType)) {
            return 'charset=' . $this->defaultCharset;
        }

        return null;
    }

    /**
     * Returns {@see true} in case of passed mime type argument supports
     * charset definition.
     *
     * @param non-empty-string $mimeType
     */
    private function supportsContentType(string $mimeType): bool
    {
        return \str_starts_with($mimeType, 'text/')
            || \in_array($mimeType, self::KNOWN_TEXT_MIME_TYPES, true);
    }
}
