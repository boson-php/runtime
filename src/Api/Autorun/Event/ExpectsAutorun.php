<?php

declare(strict_types=1);

namespace Boson\Api\Autorun\Event;

use Boson\Shared\Marker\AsApplicationEvent;

#[AsApplicationEvent]
final class ExpectsAutorun extends AutorunIntention {}
