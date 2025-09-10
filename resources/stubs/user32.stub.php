<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox\Driver\Windows;

use FFI\CData;
use FFI\CType;

/**
 * @mixin \FFI
 * @seal-properties
 * @seal-methods
 */
final readonly class User32
{
    /**
     * @param CType|non-empty-string $type
     */
    public function new(CType|string $type, bool $owned = true, bool $persistent = false): CData {}

    /**
     * @param CType|non-empty-string $type
     */
    public function cast(CType|string $type, CData|int|float|bool|null $ptr): CData {}

    public function MessageBoxW(?CData $hWnd, CData $lpText, CData $lpCaption, int $uType): int {}
}
