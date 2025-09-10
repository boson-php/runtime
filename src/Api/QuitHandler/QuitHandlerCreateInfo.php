<?php

declare(strict_types=1);

namespace Boson\Api\QuitHandler;

//
// Note:
// 1) This "$_" assign hack removes these constants from IDE autocomplete.
// 2) Only define-like constants allows object instances.
//
use Boson\Api\QuitHandler\Handler\PcntlQuitHandler;
use Boson\Api\QuitHandler\Handler\QuitHandlerInterface;
use Boson\Api\QuitHandler\Handler\WindowsQuitHandler;

\define($_ = 'Boson\Api\QuitHandler\DEFAULT_QUIT_HANDLERS', [
    new PcntlQuitHandler(),
    new WindowsQuitHandler(),
]);

/**
 * Information (configuration) DTO for creating quit handler extension.
 */
final readonly class QuitHandlerCreateInfo
{
    /**
     * @var list<QuitHandlerInterface>
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const array DEFAULT_QUIT_HANDLERS = DEFAULT_QUIT_HANDLERS;

    /**
     * @var list<QuitHandlerInterface>
     */
    public array $handlers;

    /**
     * @param iterable<mixed, QuitHandlerInterface> $handlers
     */
    public function __construct(
        iterable $handlers = self::DEFAULT_QUIT_HANDLERS,
    ) {
        $this->handlers = \iterator_to_array($handlers, false);
    }
}
