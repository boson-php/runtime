<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox\Driver\MacOS;

use FFI\CData;
use FFI\Env\Runtime;

/**
 * @mixin \FFI
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\MessageBox\Driver
 */
final readonly class LibObjectC
{
    private \FFI $ffi;

    public function __construct()
    {
        Runtime::assertAvailable();

        $this->ffi = \FFI::cdef(\trim((string) @\file_get_contents(
            filename: __FILE__,
            offset: __COMPILER_HALT_OFFSET__,
        )), 'libobjc.A.dylib');
    }

    /**
     * @param non-empty-string $return
     * @param non-empty-string ...$types
     *
     * @return CData&callable(CData, CData, mixed...):mixed
     */
    public function getMessageSend(string $return, string ...$types): callable
    {
        $suffix = $types === [] ? '...' : \implode(',', $types);

        $signature = \sprintf('%s(*)(id, SEL, %s)', $return, $suffix);

        return $this->ffi->cast($signature, $this->ffi->objc_msgSend);
    }

    /**
     * @param non-empty-string $name
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments = []): mixed
    {
        try {
            return $this->ffi->$name(...$arguments);
        } catch (\Throwable $e) {
            throw new \BadMethodCallException($e->getMessage(), previous: $e);
        }
    }

    public function __get(string $name): mixed
    {
        return $this->ffi->$name;
    }
}

__halt_compiler();

typedef void* id;
typedef void* SEL;

id objc_getClass(const char* name);
SEL sel_registerName(const char* str);
void* objc_msgSend(...);
