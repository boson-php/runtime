<?php

declare(strict_types=1);

namespace Boson\Api\Console\Event;

use Boson\Shared\Marker\AsApplicationEvent;

#[AsApplicationEvent]
final class ConsoleDetached extends ConsoleEvent {}
