<?php

declare(strict_types=1);

namespace Boson\Api;

use Boson\Application;
use Boson\Dispatcher\EventListener;

/**
 * @template-extends Extension<Application>
 */
abstract class ApplicationExtension extends Extension
{
    public function __construct(Application $context, EventListener $listener)
    {
        parent::__construct($context, $listener);
    }
}
