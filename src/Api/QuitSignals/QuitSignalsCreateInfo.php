<?php

declare(strict_types=1);

namespace Boson\Api\QuitSignals;

//
// Note:
// 1) This "$_" assign hack removes these constants from IDE autocomplete.
// 2) Only define-like constants allows object instances.
//
use Boson\Api\QuitSignals\Handler\PcntlSignalHandler;
use Boson\Api\QuitSignals\Handler\SignalHandlerInterface;
use Boson\Api\QuitSignals\Handler\WindowsSignalHandler;

\define($_ = 'Boson\Api\QuitSignals\DEFAULT_SIGNAL_HANDLERS', [
    new PcntlSignalHandler(),
    new WindowsSignalHandler(),
]);

/**
 * Information (configuration) DTO for creating quit handler extension.
 */
final readonly class QuitSignalsCreateInfo
{
    /**
     * @var list<SignalHandlerInterface>
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const array DEFAULT_SIGNAL_HANDLERS = DEFAULT_SIGNAL_HANDLERS;

    /**
     * @var list<SignalHandlerInterface>
     */
    public array $handlers;

    /**
     * @param iterable<mixed, SignalHandlerInterface> $handlers
     */
    public function __construct(
        iterable $handlers = self::DEFAULT_SIGNAL_HANDLERS,
    ) {
        $this->handlers = \iterator_to_array($handlers, false);
    }
}
