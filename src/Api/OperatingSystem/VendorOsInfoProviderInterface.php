<?php

declare(strict_types=1);

namespace Boson\Api\OperatingSystem;

use Boson\Component\OsInfo\Vendor\VendorInfoInterface;

/**
 * Provides general OS information provided by the OS.
 *
 * For example:
 *  - Name
 *  - Code Name
 *  - Version
 *  - Edition
 *  - etc...
 */
interface VendorOsInfoProviderInterface extends
    VendorInfoInterface {}
