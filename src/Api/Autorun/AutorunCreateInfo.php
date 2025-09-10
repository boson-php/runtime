<?php

declare(strict_types=1);

namespace Boson\Api\Autorun;

//
// Note:
// 1) This "$_" assign hack removes these constants from IDE autocomplete.
// 2) Only define-like constants allows object instances.
//
use Boson\Api\Autorun\Handler\AutorunHandlerInterface;
use Boson\Api\Autorun\Handler\NativeAutorunHandler;

\define($_ = 'Boson\Api\Autorun\DEFAULT_AUTORUN_HANDLERS', [
    new NativeAutorunHandler(),
]);

/**
 * Information (configuration) DTO for creating autorun extension.
 */
final readonly class AutorunCreateInfo
{
    /**
     * @var list<AutorunHandlerInterface>
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const array DEFAULT_AUTORUN_HANDLERS = DEFAULT_AUTORUN_HANDLERS;

    /**
     * @var list<AutorunHandlerInterface>
     */
    public array $handlers;

    /**
     * @param iterable<mixed, AutorunHandlerInterface> $handlers
     */
    public function __construct(
        iterable $handlers = self::DEFAULT_AUTORUN_HANDLERS,
    ) {
        $this->handlers = \iterator_to_array($handlers, false);
    }
}
