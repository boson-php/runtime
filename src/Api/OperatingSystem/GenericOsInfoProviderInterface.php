<?php

declare(strict_types=1);

namespace Boson\Api\OperatingSystem;

use Boson\Component\OsInfo\Family\FamilyProviderInterface;
use Boson\Component\OsInfo\Standard\StandardsProviderInterface;

/**
 * Provides generic OS information calculated (perhaps) based on behavior.
 *
 * For example:
 *  - Common OS family
 *  - Supported list of OS standards
 *  - etc...
 */
interface GenericOsInfoProviderInterface extends
    FamilyProviderInterface,
    StandardsProviderInterface {}
