<?php

declare(strict_types=1);

namespace Boson\Api\CentralProcessor;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Extension;

/**
 * @template-extends Extension<Application>
 */
#[AvailableAs('cpu', CentralProcessorInfoInterface::class)]
final class CentralProcessorExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): CentralProcessorInfo
    {
        return new CentralProcessorInfo();
    }
}
