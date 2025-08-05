<?php

declare(strict_types=1);

namespace Boson\Api\CentralProcessor;

use Boson\Component\CpuInfo\Vendor\VendorInfoInterface;

/**
 * Provides general CPU information provided by the CPU manufacturer.
 *
 * For example:
 *  - Name
 *  - Vendor (manufacturer)
 *  - Physical cores count
 *  - Logical cores count
 *  - etc...
 */
interface VendorCpuInfoProviderInterface extends
    VendorInfoInterface {}
