<?php

declare(strict_types=1);

namespace Boson\Api\Dialog\Event;

use Boson\Shared\Marker\AsApplicationEvent;

#[AsApplicationEvent]
final class FileSelected extends ItemSelected {}
