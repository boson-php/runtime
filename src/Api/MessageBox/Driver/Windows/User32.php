<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox\Driver\Windows;

use FFI\Env\Runtime;

/**
 * @mixin \FFI
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\MessageBox\Driver\Windows
 */
final readonly class User32
{
    public const int MB_ABORTRETRYIGNORE = 0x00000002;
    public const int MB_CANCELTRYCONTINUE = 0x00000006;
    public const int MB_HELP = 0x00004000;
    public const int MB_OK = 0x00000000;
    public const int MB_OKCANCEL = 0x00000001;
    public const int MB_RETRYCANCEL = 0x00000005;
    public const int MB_YESNO = 0x00000004;
    public const int MB_YESNOCANCEL = 0x00000003;
    public const int MB_ICONEXCLAMATION = 0x00000030;
    public const int MB_ICONWARNING = 0x00000030;
    public const int MB_ICONINFORMATION = 0x00000040;
    public const int MB_ICONASTERISK = 0x00000040;
    public const int MB_ICONQUESTION = 0x00000020;
    public const int MB_ICONSTOP = 0x00000010;
    public const int MB_ICONERROR = 0x00000010;
    public const int MB_ICONHAND = 0x00000010;
    public const int MB_DEFBUTTON1 = 0x00000000;
    public const int MB_DEFBUTTON2 = 0x00000100;
    public const int MB_DEFBUTTON3 = 0x00000200;
    public const int MB_DEFBUTTON4 = 0x00000300;
    public const int MB_APPLMODAL = 0x00000000;
    public const int MB_SYSTEMMODAL = 0x00001000;
    public const int MB_TASKMODAL = 0x00002000;
    public const int MB_DEFAULT_DESKTOP_ONLY = 0x00020000;
    public const int MB_RIGHT = 0x00080000;
    public const int MB_RTLREADING = 0x00100000;
    public const int MB_SETFOREGROUND = 0x00010000;
    public const int MB_TOPMOST = 0x00040000;
    public const int MB_SERVICE_NOTIFICATION = 0x00200000;

    /**
     * The Abort button was selected.
     */
    public const int IDABORT = 3;

    /**
     * The Cancel button was selected.
     */
    public const int IDCANCEL = 2;

    /**
     * The Continue button was selected.
     */
    public const int IDCONTINUE = 11;

    /**
     * The Ignore button was selected.
     */
    public const int IDIGNORE = 5;

    /**
     * The No button was selected.
     */
    public const int IDNO = 7;

    /**
     * The OK button was selected.
     */
    public const int IDOK = 1;

    /**
     * The Retry button was selected.
     */
    public const int IDRETRY = 4;

    /**
     * The Try Again button was selected.
     */
    public const int IDTRYAGAIN = 10;

    /**
     * The Yes button was selected.
     */
    public const int IDYES = 6;

    private \FFI $ffi;

    public function __construct()
    {
        Runtime::assertAvailable();

        $this->ffi = \FFI::cdef((string) @\file_get_contents(
            filename: __FILE__,
            offset: __COMPILER_HALT_OFFSET__,
        ), 'user32.dll');
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
}

__halt_compiler();

typedef unsigned short wchar_t;
typedef void *PVOID;
typedef PVOID HANDLE;
typedef HANDLE HWND;
typedef wchar_t WCHAR;
typedef const WCHAR *LPCWSTR;
typedef unsigned int UINT;

int MessageBoxW(
    HWND    hWnd,
    LPCWSTR lpText,
    LPCWSTR lpCaption,
    UINT    uType
);
