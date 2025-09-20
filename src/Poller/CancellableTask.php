<?php

declare(strict_types=1);

namespace Boson\Poller;

/**
 * @template TTaskId of array-key = array-key
 *
 * @template-implements CancellableTaskInterface<TTaskId>
 */
final class CancellableTask implements CancellableTaskInterface, \Stringable
{
    public private(set) bool $isCancelled = false;

    public function __construct(
        /**
         * @var PollerInterface<TTaskId>
         */
        private readonly PollerInterface $parent,
        /**
         * @var array-key
         */
        public readonly string|int $id,
    ) {}

    public function cancel(): void
    {
        $this->isCancelled = true;
        $this->parent->cancel($this);
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
