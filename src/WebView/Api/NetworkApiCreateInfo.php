<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

/**
 * Configuration class for Network API initialization.
 */
final readonly class NetworkApiCreateInfo
{
    /**
     * Default value for enabling network events.
     *
     * When set to {@see false}, network-related events
     * will not be dispatched.
     *
     * Note: All network-related events are disabled by default to
     *       optimize and speed up the application.
     */
    public const bool DEFAULT_ENABLE_EVENTS = false;

    public function __construct(
        /**
         * Whether to enable network-related events.
         */
        public bool $enableEvents = self::DEFAULT_ENABLE_EVENTS,
    ) {}
}
