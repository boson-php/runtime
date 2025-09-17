<?php

declare(strict_types=1);

namespace Boson\Api\Console\Driver\Windows {

    use FFI\CData;
    use FFI\CType;

    /**
     * @mixin \FFI
     * @seal-properties
     * @seal-methods
     */
    final readonly class Kernel32
    {
        /**
         * @param CType|non-empty-string $type
         */
        public function new(CType|string $type, bool $owned = true, bool $persistent = false): CData {}

        /**
         * @param CType|non-empty-string $type
         */
        public function cast(CType|string $type, CData|int|float|bool|null $ptr): CData {}

        public function FreeConsole(): bool {}
    }

}

namespace Boson\Api\MessageBox\Driver\Windows {


    use FFI\CData;
    use FFI\CType;

    /**
     * @mixin \FFI
     * @seal-properties
     * @seal-methods
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
        public const int IDABORT = 3;
        public const int IDCANCEL = 2;
        public const int IDCONTINUE = 11;
        public const int IDIGNORE = 5;
        public const int IDNO = 7;
        public const int IDOK = 1;
        public const int IDRETRY = 4;
        public const int IDTRYAGAIN = 10;
        public const int IDYES = 6;

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

}

namespace Boson\Api\MessageBox\Driver\MacOS {

    use FFI\CData;
    use FFI\CType;

    /**
     * @mixin \FFI
     * @seal-properties
     * @seal-methods
     */
    final readonly class LibObjectC
    {
        /**
         * @param CType|non-empty-string $type
         */
        public function new(CType|string $type, bool $owned = true, bool $persistent = false): CData {}

        /**
         * @param CType|non-empty-string $type
         */
        public function cast(CType|string $type, CData|int|float|bool|null $ptr): CData {}

        /**
         * @var CData & callable(mixed...):CData
         */
        public CData $objc_msgSend;

        /**
         * @param non-empty-string $return
         * @param non-empty-string ...$types
         *
         * @return CData & callable(CData, CData, mixed...):mixed
         */
        public function getMessageSend(string $return, string ...$types): callable {}

        /**
         * @param non-empty-string|null $name
         */
        public function objc_getClass(?string $name): CData {}

        /**
         * @param non-empty-string|null $str
         */
        public function sel_registerName(?string $str): CData {}

        public function objc_msgSend(mixed ...$args): CData {}

    }

}
