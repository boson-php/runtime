<?php

declare(strict_types=1);

namespace Boson\Extension\Attribute;

use Boson\Extension\ExtensionInterface;

/**
 * @phpstan-import-type DependencyType from ExtensionInterface
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final readonly class DependsOn
{
    /**
     * @var list<DependencyType>
     */
    public array $dependencies;

    /**
     * @param DependencyType $extension
     * @param DependencyType ...$other
     */
    public function __construct(
        string $extension,
        string ...$other,
    ) {
        $this->dependencies = \array_values([$extension, ...$other]);
    }
}
