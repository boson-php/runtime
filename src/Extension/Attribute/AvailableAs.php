<?php

declare(strict_types=1);

namespace Boson\Extension\Attribute;

use Boson\Extension\ExtensionProviderInterface;

/**
 * @phpstan-import-type AliasType from ExtensionProviderInterface
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final readonly class AvailableAs
{
    /**
     * @var list<AliasType>
     */
    public array $aliases;

    /**
     * @param AliasType|iterable<mixed, AliasType> $alias
     */
    public function __construct(
        string|iterable $alias,
    ) {
        $this->aliases = match (true) {
            \is_string($alias) => [$alias],
            \is_array($alias) => \array_values($alias),
            default => \iterator_to_array($alias, false),
        };
    }
}
