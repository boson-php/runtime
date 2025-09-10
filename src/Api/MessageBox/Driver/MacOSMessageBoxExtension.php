<?php

declare(strict_types=1);

namespace Boson\Api\MessageBox\Driver;

use Boson\Api\MessageBox\Driver\MacOS\LibObjectC;
use Boson\Api\MessageBox\MessageBoxCreateInfo;
use Boson\Api\MessageBox\MessageBoxExtensionInterface;
use Boson\Api\MessageBox\MessageBoxIcon;
use FFI\CData;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

final readonly class MacOSMessageBoxExtension implements MessageBoxExtensionInterface
{
    private CData $msgSendId;
    private CData $msgSendString;
    private CData $msgSendLong;

    public function __construct(
        private LibObjectC $libobjc = new LibObjectC(),
    ) {
        $this->msgSendId = $libobjc->getMessageSend('id');
        $this->msgSendString = $libobjc->getMessageSend('const char*');
        $this->msgSendLong = $libobjc->getMessageSend('long');
    }

    public function create(MessageBoxCreateInfo $info): PromiseInterface
    {
        // NSString *titleStr = [NSString stringWithUTF8String:title]
        $titleStr = ($this->msgSendString)(
            $this->libobjc->objc_getClass('NSString'),
            $this->libobjc->sel_registerName('stringWithUTF8String:'),
            $info->title . "\0",
        );

        // NSString *textStr = [NSString stringWithUTF8String:text]
        $textStr = ($this->msgSendString)(
            $this->libobjc->objc_getClass('NSString'),
            $this->libobjc->sel_registerName('stringWithUTF8String:'),
            $info->text . "\0",
        );

        // NSAlert *alert = [NSAlert new]
        $alert = $this->libobjc->objc_msgSend(
            $this->libobjc->objc_getClass('NSAlert'),
            $this->libobjc->sel_registerName('new'),
        );

        // [alert setMessageText:textStr];
        ($this->msgSendId)(
            $alert,
            $this->libobjc->sel_registerName('setMessageText:'),
            $textStr,
        );

        // [alert setInformativeText:titleStr];
        ($this->msgSendId)(
            $alert,
            $this->libobjc->sel_registerName('setInformativeText:'),
            $titleStr,
        );

        // Set alert style based on icon
        if ($info->icon !== null) {
            // [alert setAlertStyle:alertStyle];
            ($this->msgSendLong)(
                $alert,
                $this->libobjc->sel_registerName('setAlertStyle:'),
                match ($info->icon) {
                    MessageBoxIcon::Error => 0,   // NSAlertStyleCritical
                    MessageBoxIcon::Warning => 1, // NSAlertStyleWarning
                    MessageBoxIcon::Info => 2,    // NSAlertStyleInformational
                },
            );
        }

        // [alert runModal];
        $this->libobjc->objc_msgSend(
            $alert,
            $this->libobjc->sel_registerName('runModal'),
        );

        return resolve(null);
    }
}
