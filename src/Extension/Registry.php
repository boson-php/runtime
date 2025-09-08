<?php

declare(strict_types=1);

namespace Boson\Extension;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Exception\ExtensionAlreadyLoadedException;
use Boson\Extension\Exception\ExtensionLoadingException;
use Boson\Extension\Exception\ExtensionNotFoundException;
use Boson\Extension\Loader\DependencyGraph;
use Psr\Container\ContainerInterface;

/**
 * @template TContext of IdentifiableInterface
 */
final class Registry implements ContainerInterface
{
    /**
     * @var array<non-empty-string, object>
     */
    private array $extensions = [];

    /**
     * @var list<ExtensionProviderInterface<TContext>>
     */
    private array $providers = [];

    /**
     * @var array<non-empty-string, object>
     */
    private array $properties = [];

    private bool $booted = false;

    /**
     * @param TContext $context
     * @param iterable<array-key, ExtensionProviderInterface<TContext>> $providers
     *
     * @throws ExtensionLoadingException
     */
    public function __construct(
        private readonly IdentifiableInterface $context,
        private readonly EventListener $listener,
        iterable $providers = [],
    ) {
        $this->providers = \iterator_to_array($providers, false);
    }

    /**
     * @return array<non-empty-string, object>
     * @throws ExtensionLoadingException
     */
    public function boot(): array
    {
        if ($this->booted === true) {
            return $this->properties;
        }

        foreach (new DependencyGraph($this->providers) as $provider) {
            try {
                $extension = $provider->load($this->context, $this->listener);
            } catch (\Throwable $e) {
                throw ExtensionLoadingException::becauseLoadingExceptionOccurs($e);
            }

            $this->extensions[$extension::class] = $extension;

            foreach ($provider->aliases as $alias) {
                if (isset($this->extensions[$alias]) && $alias !== $extension::class) {
                    throw ExtensionAlreadyLoadedException::becauseExtensionAlreadyLoaded($alias);
                }

                if ($this->isProperty($alias)) {
                    $this->properties[$alias] = $extension;
                }

                $this->extensions[$alias] = $extension;
            }
        }

        $this->providers = [];
        $this->booted = true;

        return $this->properties;
    }

    private function isProperty(string $name): bool
    {
        if ($name === '') {
            return false;
        }

        \preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/isu', $name, $matches);

        return isset($matches[0]);
    }

    /**
     * @template TArgService of object
     *
     * @param class-string<TArgService>|string $id
     *
     * @return ($id is class-string<TArgService> ? TArgService : object)
     * @throws ExtensionNotFoundException
     */
    public function get(string $id): object
    {
        return $this->extensions[$id]
            ?? throw ExtensionNotFoundException::becauseExtensionNotFound($id);
    }

    public function has(string $id): bool
    {
        return isset($this->extensions[$id]);
    }
}
