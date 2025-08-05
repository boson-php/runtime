<?php

declare(strict_types=1);

namespace Boson\Api;

use Boson\Api\CentralProcessor\AdvancedCpuInfoProviderInterface;
use Boson\Api\CentralProcessor\GenericCpuInfoProviderInterface;
use Boson\Api\CentralProcessor\VendorCpuInfoProviderInterface;

/**
 * Provides information about the main CPU.
 */
interface CentralProcessorApiInterface extends
    GenericCpuInfoProviderInterface,
    VendorCpuInfoProviderInterface,
    AdvancedCpuInfoProviderInterface {}
