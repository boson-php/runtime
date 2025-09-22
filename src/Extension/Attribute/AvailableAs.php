<?php

declare(strict_types=1);

namespace Boson\Extension\Attribute;

use Boson\Extension\ExtensionInterface;

/**
 * @phpstan-import-type AliasType from ExtensionInterface
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final readonly class AvailableAs
{
    /**
     * @var list<AliasType>
     */
    public array $aliases;

    /**
     * @param AliasType $alias
     * @param AliasType ...$other
     */
    public function __construct(
        string $alias,
        string ...$other,
    ) {
        $this->aliases = \array_values([$alias, ...$other]);
    }
}
