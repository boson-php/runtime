<?php

declare(strict_types=1);

namespace Boson\Internal\Poller;

use Boson\ApplicationId;
use Boson\Internal\Saucer\SaucerInterface;
use Boson\Poller\PollerInterface;
use Boson\Poller\Suspension;
use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\IdValueGenerator\PlatformDependentIntValueGenerator;
use FFI\CData;

/**
 * @api
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final class SaucerPoller implements PollerInterface
{
    /**
     * @var array<array-key, \Closure(array-key):void>
     */
    private array $queueTasks = [];

    /**
     * @var array<array-key, \Closure(array-key):void>
     */
    private array $periodicTasks = [];

    private TaskType $type = TaskType::Internal;

    private readonly IdValueGeneratorInterface $ids;

    private readonly CData $ptr;

    public function __construct(
        private readonly ApplicationId $id,
        private readonly SaucerInterface $saucer,
    ) {
        $this->ids = new PlatformDependentIntValueGenerator();
        $this->ptr = $this->id->ptr;
    }

    public function createSuspension(): Suspension
    {
        return new Suspension($this);
    }

    public function next(): void
    {
        switch ($this->type) {
            case TaskType::Internal:
                $this->executeInternalTask();
                break;

            case TaskType::Periodic:
                $this->executePeriodicTask();
                break;

            case TaskType::Queued:
                $this->executeQueuedTask();
                break;
        }

        $this->type = $this->type->next();
    }

    private function executeInternalTask(): void
    {
        $this->saucer->saucer_application_run_once($this->ptr);
    }

    private function executePeriodicTask(): void
    {
        foreach ($this->periodicTasks as $id => $task) {
            $task($id);

            return;
        }
    }

    private function executeQueuedTask(): void
    {
        foreach ($this->queueTasks as $id => $task) {
            unset($this->queueTasks[$id]);

            $task($id);

            return;
        }
    }

    public function defer(callable $task): int|string
    {
        $this->queueTasks[$id = $this->ids->nextId()] = $task(...);

        return $id;
    }

    public function repeat(callable $task): int|string
    {
        $this->periodicTasks[$id = $this->ids->nextId()] = $task(...);

        return $id;
    }

    public function delay(float $delay, callable $task): int|string
    {
        $stopsAfter = \microtime(true) + $delay;

        return $this->repeat(function (string|int $taskId) use ($stopsAfter, $task): void {
            if (\microtime(true) > $stopsAfter) {
                $task($taskId);

                $this->cancel($taskId);
            }
        });
    }

    public function cancel(int|string $taskId): void
    {
        unset($this->periodicTasks[$taskId], $this->queueTasks[$taskId]);
    }
}
