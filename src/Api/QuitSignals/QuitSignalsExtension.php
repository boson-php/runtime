<?php

declare(strict_types=1);

namespace Boson\Api\QuitSignals;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Extension;

/**
 * Registers platform-specific quit handlers during application boot.
 *
 * An extension is responsible for processing signals such as `ctrl+c`
 * (in terminal) or `kill process-name`.
 *
 * @template-extends Extension<Application>
 */
final class QuitSignalsExtension extends Extension
{
    /**
     * An extension can only be registered once.
     */
    private static bool $isRegistered = false;

    public function __construct(
        private readonly QuitSignalsCreateInfo $info = new QuitSignalsCreateInfo(),
    ) {}

    public function load(IdentifiableInterface $ctx, EventListener $listener): null
    {
        if (self::$isRegistered === true) {
            return null;
        }

        foreach ($this->info->handlers as $handler) {
            if ($handler->register($ctx->quit(...))) {
                break;
            }
        }

        self::$isRegistered = true;

        return null;
    }
}
