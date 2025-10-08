<?php

declare(strict_types=1);

namespace Boson\Api\CentralProcessor;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\InstructionSetInterface;

/**
 * Provides information about the main CPU.
 */
interface CentralProcessorInfoInterface
{
    /**
     * Gets current CPU architecture type
     */
    public ArchitectureInterface $arch { get; }

    /**
     * Gets current CPU generic vendor name.
     *
     * @var non-empty-string
     */
    public string $vendor { get; }

    /**
     * Gets current CPU name.
     *
     * @var non-empty-string|null
     */
    public ?string $name { get; }

    /**
     * Gets the number of physical CPU cores.
     *
     * @var int<1, max>
     */
    public int $cores { get; }

    /**
     * Gets the number of logical CPU cores.
     *
     * Note: The number of logical cores can be equal to or greater
     *       than the number of physical cores ({@see $cores}).
     *
     * @var int<1, max>
     */
    public int $threads { get; }

    /**
     * Gets list of supported processor instructions
     *
     * @var iterable<array-key, InstructionSetInterface>
     */
    public iterable $instructionSets { get; }

    /**
     * Checks if this CPU supports the given instruction set.
     *
     * @api
     */
    public function isSupports(InstructionSetInterface $instructionSet): bool;
}
