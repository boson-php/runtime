<?php

declare(strict_types=1);

namespace Boson\Extension\Loader;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Extension\ExtensionInterface;

/**
 * Resolves dependencies between extension providers and
 * computes a safe load order.
 *
 * @template TContext of IdentifiableInterface
 *
 * @phpstan-type ProviderType ExtensionInterface<TContext>
 * @phpstan-type ProviderIdType class-string<ProviderType>
 * @phpstan-type ProvidersMapType array<ProviderIdType, ProviderType>
 *
 * @template-implements \IteratorAggregate<array-key, ProviderType>
 */
final class DependencyGraph implements \IteratorAggregate
{
    /**
     * Map of loaded providers keyed by provider class name.
     *
     * @var ProvidersMapType
     */
    private array $loaded = [];

    /**
     * Stack representing the current dependency resolution path.
     *
     * @var \SplStack<ProviderType>
     */
    private readonly \SplStack $dependencies;

    /**
     * @param iterable<mixed, ProviderType> $providers
     */
    public function __construct(iterable $providers)
    {
        $this->dependencies = new \SplStack();

        $registered = $this->createProvidersMap($providers);

        foreach ($providers as $provider) {
            $this->register($provider, $registered);
        }
    }

    /**
     * Return the current resolution chain as a list of provider class names.
     *
     * @return list<ProviderIdType>
     */
    private function getProgressStackAsArray(): array
    {
        $result = [];

        foreach ($this->dependencies as $stack) {
            $result[] = $stack::class;
        }

        return \array_reverse($result);
    }

    /**
     * Ensure there is no recursion in the dependency chain for the given provider.
     *
     * @param ProviderType $provider
     */
    private function assertHasNoRecursiveDependencies(ExtensionInterface $provider): void
    {
        foreach ($this->dependencies as $stack) {
            if ($stack !== $provider) {
                continue;
            }

            throw new \LogicException(\sprintf(
                '%s cannot be loaded because it contains recursion dependencies in order %s',
                $provider::class,
                \implode(' > ', $this->getProgressStackAsArray()),
            ));
        }
    }

    /**
     * Prepare provider processing by pushing it into the resolution stack.
     *
     * @param ProviderType $provider
     */
    private function prepare(ExtensionInterface $provider): void
    {
        $this->assertHasNoRecursiveDependencies($provider);

        $this->dependencies->push($provider);
    }

    /**
     * Complete provider processing by marking it loaded and
     * popping from the stack.
     *
     * @param ProviderType $provider
     */
    private function complete(ExtensionInterface $provider): void
    {
        $this->markAsLoaded($provider);

        $this->dependencies->pop();
    }

    /**
     * Record the provider as loaded if not already present.
     *
     * @param ProviderType $provider
     */
    private function markAsLoaded(ExtensionInterface $provider): void
    {
        if (isset($this->loaded[$provider::class])) {
            return;
        }

        $this->loaded[$provider::class] = $provider;
    }

    /**
     * Recursively resolve and register all dependencies for the given provider.
     *
     * @param ProviderType $provider
     * @param ProvidersMapType $registered
     */
    private function register(ExtensionInterface $provider, array $registered): void
    {
        $this->prepare($provider);

        foreach ($provider->dependencies as $dependency) {
            if (!isset($registered[$dependency])) {
                throw new \OutOfBoundsException(\sprintf(
                    'Dependency %s of extension %s is not registered',
                    $dependency,
                    $provider::class,
                ));
            }

            $this->register($registered[$dependency], $registered);
        }

        $this->complete($provider);
    }

    /**
     * Build a class-name keyed map of providers, ensuring uniqueness.
     *
     * @param iterable<mixed, ProviderType> $providers
     *
     * @return ProvidersMapType
     */
    private function createProvidersMap(iterable $providers): array
    {
        $result = [];

        foreach ($providers as $provider) {
            if (isset($result[$provider::class])) {
                throw new \LogicException(\sprintf(
                    'Extension provider "%s" already loaded',
                    $provider::class,
                ));
            }

            $result[$provider::class] = $provider;
        }

        return $result;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->loaded);
    }
}
