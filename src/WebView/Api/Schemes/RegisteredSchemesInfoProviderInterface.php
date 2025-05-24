<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes;

interface RegisteredSchemesInfoProviderInterface
{
    /**
     * Contains a list of registered schemes.
     *
     * @var list<non-empty-lowercase-string>
     */
    public array $schemes {
        get;
    }
}
