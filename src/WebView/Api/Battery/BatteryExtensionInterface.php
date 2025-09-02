<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery;

use Boson\Shared\Marker\ExpectsSecurityContext;

#[ExpectsSecurityContext]
interface BatteryExtensionInterface extends
    BatteryInfoProviderInterface {}
