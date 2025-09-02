<?php

declare(strict_types=1);

namespace Boson\Extension;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
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
     * @var list<object>
     */
    private array $extensions = [];

    /**
     * @var list<ExtensionProviderInterface<TContext>>
     */
    private array $providers = [];

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
     * @throws ExtensionLoadingException
     */
    public function boot(): void
    {
        if ($this->booted === true) {
            return;
        }

        foreach (new DependencyGraph($this->providers) as $provider) {
            try {
                $extension = $provider->load($this->context, $this->listener);
            } catch (\Throwable $e) {
                throw ExtensionLoadingException::becauseLoadingExceptionOccurs($e);
            }

            $this->extensions[$extension::class] = $extension;
        }

        $this->providers = [];
        $this->booted = true;
    }

    /**
     * @template TArgService of object
     *
     * @param class-string<TArgService> $id
     *
     * @return TArgService
     * @throws ExtensionNotFoundException
     */
    public function get(string $id): object
    {
        return $this->extensions[$id]
            ?? throw ExtensionNotFoundException::becauseExceptionNotFound($id);
    }

    /**
     * @param class-string $id
     */
    public function has(string $id): bool
    {
        return isset($this->extensions[$id]);
    }
}
