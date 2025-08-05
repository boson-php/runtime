<?php

declare(strict_types=1);

namespace Boson\Api;

use Boson\Contracts\EventListener\Subscription\CancellableSubscriptionInterface;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\Event;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\Intention;
use Boson\Internal\StructPointerId;
use FFI\CData;

/**
 * @template T of IdentifiableInterface<StructPointerId>
 */
abstract class Extension
{
    protected StructPointerId $id {
        /** @phpstan-ignore-next-line : Context is a "IdentifiableInterface<StructPointerId>" */
        get => $this->context->id;
    }

    protected CData $ptr {
        get => $this->id->ptr;
    }

    public function __construct(
        /**
         * @var T
         */
        protected readonly IdentifiableInterface $context,
        private readonly EventListener $listener,
    ) {}

    /**
     * @template TArgEvent of object
     *
     * @param class-string<TArgEvent> $event the event (class) name
     * @param callable(TArgEvent):void $then the listener callback
     *
     * @return CancellableSubscriptionInterface<TArgEvent>
     */
    protected function listen(string $event, callable $then): CancellableSubscriptionInterface
    {
        return $this->listener->addEventListener($event, $then);
    }

    /**
     * @param Intention<T> $intention
     */
    protected function intent(object $intention): bool
    {
        $this->listener->dispatch($intention);

        return $intention->isCancelled === false;
    }

    /**
     * @param Event<T> $event
     */
    protected function dispatch(object $event): void
    {
        $this->listener->dispatch($event);
    }
}
