<?php

declare(strict_types=1);

namespace Boson\Extension;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Attribute\DependsOn;

/**
 * @template TContext of IdentifiableInterface = IdentifiableInterface
 *
 * @template-implements ExtensionInterface<TContext>
 *
 * @phpstan-import-type DependencyType from ExtensionInterface
 * @phpstan-import-type AliasType from ExtensionInterface
 */
abstract class Extension implements ExtensionInterface
{
    /**
     * @var list<AliasType>
     */
    public array $aliases {
        get => $this->aliases ??= $this->getAliasesFromAttributes();
    }

    /**
     * @var list<DependencyType>
     */
    public array $dependencies {
        get => $this->dependencies ??= $this->getDependenciesFromAttributes();
    }

    /**
     * @return list<AliasType>
     */
    private function getAliasesFromAttributes(): array
    {
        $aliases = [];

        foreach ($this->getClassAttributes(AvailableAs::class) as $attr) {
            $aliases = \array_merge($aliases, $attr->aliases);
        }

        return $aliases;
    }

    /**
     * @return list<DependencyType>
     */
    private function getDependenciesFromAttributes(): array
    {
        $aliases = [];

        foreach ($this->getClassAttributes(DependsOn::class) as $attr) {
            $aliases = \array_merge($aliases, $attr->dependencies);
        }

        return $aliases;
    }

    /**
     * @template TArgAttribute of object
     *
     * @param class-string<TArgAttribute> $class
     *
     * @return iterable<array-key, TArgAttribute>
     */
    private function getClassAttributes(string $class): iterable
    {
        $reflectionAttributes = new \ReflectionClass(static::class)
            ->getAttributes($class);

        foreach ($reflectionAttributes as $reflectionAttribute) {
            yield $reflectionAttribute->newInstance();
        }
    }
}
