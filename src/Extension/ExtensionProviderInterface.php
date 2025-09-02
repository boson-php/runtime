<?php

declare(strict_types=1);

namespace Boson\Extension;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;

/**
 * @template TContext of IdentifiableInterface = IdentifiableInterface
 */
interface ExtensionProviderInterface
{
    /**
     * @var list<class-string<ExtensionProviderInterface>>
     */
    public array $dependencies {
        get;
    }

    /**
     * @param TContext $ctx
     */
    public function load(IdentifiableInterface $ctx, EventListener $listener): object;
}
