<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Extension;

/**
 * @template-extends Extension<Application>
 */
#[AvailableAs('dialog', DialogApiInterface::class)]
final class DialogExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): SaucerDialogApi
    {
        return new SaucerDialogApi($ctx, $listener);
    }
}
