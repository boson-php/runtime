<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox\Driver;

use Boson\Api\MessageBox\Driver\MacOS\LibObjectC;
use Boson\Api\MessageBox\MessageBoxButton;
use Boson\Api\MessageBox\MessageBoxCreateInfo;
use Boson\Api\MessageBox\MessageBoxExtensionInterface;
use Boson\Api\MessageBox\MessageBoxIcon;
use FFI\CData;

final readonly class MacOSMessageBoxExtension implements MessageBoxExtensionInterface
{
    private CData $msgSendId;
    private CData $msgSendStringGetId;
    private CData $msgSendLong;
    private CData $msgSendVoidGetId;
    private CData $msgSendVoidGetLong;

    public function __construct(
        private LibObjectC $libobjc = new LibObjectC(),
    ) {
        $this->msgSendStringGetId = $libobjc->getMessageSend('id', 'const char*');
        $this->msgSendId = $libobjc->getMessageSend('id', 'id');
        $this->msgSendLong = $libobjc->getMessageSend('id', 'long');
        $this->msgSendVoidGetId = $libobjc->getMessageSend('id');
        $this->msgSendVoidGetLong = $libobjc->getMessageSend('long');
    }

    public function create(MessageBoxCreateInfo $info): ?MessageBoxButton
    {
        // NSString *titleStr = [NSString stringWithUTF8String:title]
        $titleStr = ($this->msgSendStringGetId)(
            $this->libobjc->objc_getClass('NSString'),
            $this->libobjc->sel_registerName('stringWithUTF8String:'),
            $info->title . "\0",
        );

        // NSString *textStr = [NSString stringWithUTF8String:text]
        $textStr = ($this->msgSendStringGetId)(
            $this->libobjc->objc_getClass('NSString'),
            $this->libobjc->sel_registerName('stringWithUTF8String:'),
            $info->text . "\0",
        );

        // NSAlert *alert = [NSAlert new]
        $alert = ($this->msgSendVoidGetId)(
            $this->libobjc->objc_getClass('NSAlert'),
            $this->libobjc->sel_registerName('new'),
        );

        // [alert setMessageText:titleStr];
        ($this->msgSendId)(
            $alert,
            $this->libobjc->sel_registerName('setMessageText:'),
            $titleStr,
        );

        // [alert setInformativeText:textStr];
        ($this->msgSendId)(
            $alert,
            $this->libobjc->sel_registerName('setInformativeText:'),
            $textStr,
        );

        // Set alert style based on icon
        if ($info->icon !== null) {
            ($this->msgSendLong)(
                $alert,
                $this->libobjc->sel_registerName('setAlertStyle:'),
                match ($info->icon) {
                    MessageBoxIcon::Error => 2,   // NSAlertStyleCritical
                    MessageBoxIcon::Warning => 0, // NSAlertStyleWarning
                    MessageBoxIcon::Info => 1,    // NSAlertStyleInformational
                },
            );
        }

        // Add buttons based on cancel flag
        if ($info->cancel) {
            // [alert addButtonWithTitle:@"Cancel"];
            ($this->msgSendId)(
                $alert,
                $this->libobjc->sel_registerName('addButtonWithTitle:'),
                ($this->msgSendStringGetId)(
                    $this->libobjc->objc_getClass('NSString'),
                    $this->libobjc->sel_registerName('stringWithUTF8String:'),
                    "Cancel\0",
                ),
            );
        }

        // [alert addButtonWithTitle:@"OK"];
        ($this->msgSendId)(
            $alert,
            $this->libobjc->sel_registerName('addButtonWithTitle:'),
            ($this->msgSendStringGetId)(
                $this->libobjc->objc_getClass('NSString'),
                $this->libobjc->sel_registerName('stringWithUTF8String:'),
                "OK\0",
            ),
        );

        // [alert runModal];
        $result = ($this->msgSendVoidGetLong)(
            $alert,
            $this->libobjc->sel_registerName('runModal'),
        );

        return match ($result) {
            1000 => $info->cancel ? MessageBoxButton::Cancel : MessageBoxButton::Ok,
            1001 => MessageBoxButton::Ok,
            default => $info->cancel,
        };
    }
}
