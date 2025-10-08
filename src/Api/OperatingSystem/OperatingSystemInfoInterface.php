<?php

declare(strict_types=1);

namespace Boson\Api\OperatingSystem;

use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\StandardInterface;

/**
 * Provides information about the current OS.
 */
interface OperatingSystemInfoInterface
{
    /**
     * Gets the family this operating system belongs to.
     */
    public FamilyInterface $family { get; }

    /**
     * Gets the name of the operating system.
     *
     * The name should be a non-empty string that uniquely identifies this
     * operating system. For example, "Ubuntu 22.04 LTS" or "Windows 11".
     *
     * @var non-empty-string
     */
    public string $name { get; }

    /**
     * Gets the version of the operating system.
     *
     * @var non-empty-string
     */
    public string $version { get; }

    /**
     * Gets the codename of the operating system.
     *
     * @var non-empty-string|null
     */
    public ?string $codename { get; }

    /**
     * Gets the edition of the operating system.
     *
     * @var non-empty-string|null
     */
    public ?string $edition { get; }

    /**
     * Gets the list of standards supported by this operating system.
     *
     * @var iterable<array-key, StandardInterface>
     */
    public iterable $standards { get; }

    /**
     * Checks if this operating system supports the given standard.
     *
     * This method checks if any of the standards supported by this operating
     * system (including standards of its family) supports the given standard.
     *
     * @api
     */
    public function isSupports(StandardInterface $standard): bool;
}
