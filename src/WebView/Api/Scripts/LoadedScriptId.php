<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use Boson\Internal\Saucer\SaucerInterface;
use Boson\Internal\StructPointerId;
use FFI\CData;

final readonly class LoadedScriptId extends StructPointerId
{
    /**
     * Returns new {@see LoadedScriptId} instance from given
     * `saucer_script*` struct pointer.
     *
     * @api
     */
    final public static function fromScriptHandle(SaucerInterface $api, CData $handle): self
    {
        $id = self::getPointerIntValue($api, $handle);

        return new self($id, $handle);
    }
}
