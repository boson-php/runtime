<?php

declare(strict_types=1);

namespace Boson\Window\Api;

use Boson\Api\Extension;
use Boson\Dispatcher\EventListener;
use Boson\Window\Window;

/**
 * @template-extends Extension<Window>
 */
abstract class WindowExtension extends Extension
{
    public function __construct(Window $context, EventListener $listener)
    {
        parent::__construct($context, $listener);
    }
}
