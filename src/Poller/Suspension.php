<?php

declare(strict_types=1);

namespace Boson\Poller;

/**
 * @template T of mixed = mixed
 *
 * @template-implements SuspensionInterface<T>
 */
final class Suspension implements SuspensionInterface
{
    private bool $isResolved = false;

    /**
     * @var T
     */
    private mixed $result;

    public function __construct(
        private readonly PollerInterface $parent,
    ) {}

    public function resolve(mixed $result): void
    {
        $this->isResolved = true;
        $this->result = $result;
    }

    public function reject(\Throwable $error): void
    {
        $this->parent->throw($error);
    }

    public function suspend(): mixed
    {
        while ($this->isResolved === false) {
            $this->parent->next();
        }

        return $this->result;
    }
}
