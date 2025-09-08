<?php

declare(strict_types=1);

namespace Boson\Extension;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;

/**
 * @phpstan-type DependencyType class-string<ExtensionProviderInterface>
 * @phpstan-type AliasType non-empty-string
 *
 * @template TContext of IdentifiableInterface = IdentifiableInterface
 */
interface ExtensionProviderInterface
{
    /**
     * List of extension providers on which the specified extension depends.
     *
     * Specifying dependencies allows you to explicitly use
     * them in your extension. For example, when specifying
     * an "ExampleDependencyProvider":
     *
     * ```php
     * public array $dependencies = [
     *     ExampleDependencyProvider::class,
     * ];
     * ```
     *
     * You can depend on it obviously:
     *
     * ```php
     * public function load(IdentifiableInterface $ctx, EventListener $listener): object
     * {
     *     return new MyExtension(
     *         // The specified "get()" call will not throw errors,
     *         // since the extension will be loaded earlier.
     *         dependency: $ctx->get(ExampleDependency::class),
     *     );
     * }
     * ```
     *
     * @var list<class-string<ExtensionProviderInterface>>
     *
     * @phpstan-var list<DependencyType>
     */
    public array $dependencies {
        get;
    }

    /**
     * List of aliases under which the specified extension
     * will be available.
     *
     * If you specify an alias without special characters (such as "example"),
     * the extension will be available for use explicitly using the property:
     *
     * ```php
     * $app->example;          // In case of Application Extension
     * $app->window->example;  // In case of Window Extension
     * $app->webview->example; // In case of WebView Extension
     * ```
     *
     * @var list<non-empty-string>
     *
     * @phpstan-var list<AliasType>
     */
    public array $aliases {
        get;
    }

    /**
     * Loads the extension for the requested context.
     *
     * @param TContext $ctx
     */
    public function load(IdentifiableInterface $ctx, EventListener $listener): object;
}
