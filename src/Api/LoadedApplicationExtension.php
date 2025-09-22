<?php

declare(strict_types=1);

namespace Boson\Api;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\LoadedExtension;
use Boson\Internal\StructPointerId;
use FFI\CData;

/**
 * @template TContext of IdentifiableInterface<StructPointerId> = Application
 *
 * @template-extends LoadedExtension<TContext>
 */
abstract class LoadedApplicationExtension extends LoadedExtension
{
    /**
     * Gets reference to the context's ID
     */
    protected StructPointerId $id {
        get => $this->app->id;
    }

    /**
     * Gets reference to the context's pointer
     */
    protected CData $ptr {
        get => $this->id->ptr;
    }

    public function __construct(
        protected readonly Application $app,
        EventListener $listener,
    ) {
        parent::__construct($listener);
    }
}
