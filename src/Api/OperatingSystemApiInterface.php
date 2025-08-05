<?php

declare(strict_types=1);

namespace Boson\Api;

use Boson\Api\OperatingSystem\GenericOsInfoProviderInterface;
use Boson\Api\OperatingSystem\VendorOsInfoProviderInterface;

/**
 * Provides information about the current OS.
 */
interface OperatingSystemApiInterface extends
    GenericOsInfoProviderInterface,
    VendorOsInfoProviderInterface {}
