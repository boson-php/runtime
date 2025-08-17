<?php

declare(strict_types=1);

namespace Boson\Window\Api;

use Boson\Api\ApplicationExtension;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Internal\StructPointerId;
use Boson\Window\Window;

/**
 * @template TContext of IdentifiableInterface<StructPointerId> = Window
 *
 * @template-extends ApplicationExtension<TContext>
 */
abstract class WindowExtension extends ApplicationExtension
{
    /**
     * Gets reference to the context's ID
     */
    protected StructPointerId $id {
        #[\Override]
        get => $this->window->id;
    }

    public function __construct(
        protected readonly Window $window,
        EventListener $listener,
    ) {
        parent::__construct($window->app, $listener);
    }
}
