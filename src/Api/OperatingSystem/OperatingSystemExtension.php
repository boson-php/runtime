<?php

declare(strict_types=1);

namespace Boson\Api\OperatingSystem;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Contracts\OsInfo\OperatingSystemInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Extension;

/**
 * @template-extends Extension<Application>
 */
#[AvailableAs('os', OperatingSystemInfoInterface::class, OperatingSystemInterface::class)]
final class OperatingSystemExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): OperatingSystemInfo
    {
        return new OperatingSystemInfo();
    }
}
