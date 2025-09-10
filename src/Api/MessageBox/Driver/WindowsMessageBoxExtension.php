<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox\Driver;

use Boson\Api\MessageBox\Driver\Windows\User32;
use Boson\Api\MessageBox\MessageBoxButton;
use Boson\Api\MessageBox\MessageBoxCreateInfo;
use Boson\Api\MessageBox\MessageBoxExtensionInterface;
use Boson\Api\MessageBox\MessageBoxIcon;
use FFI\CData;

final readonly class WindowsMessageBoxExtension implements MessageBoxExtensionInterface
{
    public function __construct(
        private User32 $user32 = new User32(),
    ) {}

    private function string(string $text): CData
    {
        // Format text new lines
        $text = \str_replace(["\r\n", "\n"], "\r\n", $text);

        // Windows OS expects UTF-16
        $string = \iconv('utf-8', 'utf-16le', $text) . "\0\0";
        // There are 2 bytes per character
        $length = (int) (\strlen($string) / 2);

        $result = $this->user32->new("wchar_t[$length]", false);

        \FFI::memcpy($result, $string, \strlen($string));

        return $result;
    }

    public function create(MessageBoxCreateInfo $info): ?MessageBoxButton
    {
        $title = $this->string($info->title);
        $text = $this->string($info->text);

        $flags = match ($info->icon) {
            MessageBoxIcon::Error => User32::MB_ICONERROR,
            MessageBoxIcon::Warning => User32::MB_ICONWARNING,
            MessageBoxIcon::Info => User32::MB_ICONINFORMATION,
            default => 0,
        };

        if ($info->cancel) {
            $flags |= User32::MB_OKCANCEL;
        }

        $result = $this->user32->MessageBoxW(null, $text, $title, $flags);

        \FFI::free($title);
        \FFI::free($text);

        return match ($result) {
            User32::IDOK => MessageBoxButton::Ok,
            User32::IDCANCEL => MessageBoxButton::Cancel,
            default => null,
        };
    }
}
