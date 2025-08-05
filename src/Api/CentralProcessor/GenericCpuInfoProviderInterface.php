<?php

declare(strict_types=1);

namespace Boson\Api\CentralProcessor;

use Boson\Component\CpuInfo\Architecture\ArchitectureProviderInterface;

/**
 * Provides generic CPU information received from external subsystems.
 *
 * For example:
 * - A common CPU architecture (family)
 * - etc...
 */
interface GenericCpuInfoProviderInterface extends
    ArchitectureProviderInterface {}
