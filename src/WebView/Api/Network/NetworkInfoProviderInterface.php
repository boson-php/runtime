<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network;

use Boson\WebView\Api\Network\Exception\NetworkNotAvailableException;
use Boson\WebView\Api\Network\Exception\NetworkNotReadyException;

interface NetworkInfoProviderInterface
{
    /**
     * Gets the effective bandwidth estimate in megabits per second,
     * rounded to the nearest multiple of 25 kilobits per seconds.
     *
     * This value is based on recently observed application layer
     * throughput across recently active connections, excluding
     * connections made to a private address space. In the absence
     * of recent bandwidth measurement data, the attribute value
     * is determined by the properties of the underlying
     * connection technology.
     */
    public float $downlink {
        /**
         * @throws NetworkNotReadyException in case the API is not ready
         * @throws NetworkNotAvailableException in case the API is not available
         */
        get;
    }

    /**
     * Gets the maximum downlink speed, in megabits per second (Mbps),
     * for the underlying connection technology.
     *
     * The value may contain {@see null} if this information is
     * not available.
     */
    public ?float $downlinkMax {
        /**
         * @throws NetworkNotReadyException in case the API is not ready
         * @throws NetworkNotAvailableException in case the API is not available
         */
        get;
    }

    /**
     * Gets the effective type of the connection.
     *
     * This value is determined using a combination of recently
     * observed, round-trip time and downlink values.
     */
    public NetworkEffectiveType $effectiveType {
        /**
         * @throws NetworkNotReadyException in case the API is not ready
         * @throws NetworkNotAvailableException in case the API is not available
         */
        get;
    }

    /**
     * Gets the estimated effective round-trip time of the current
     * connection, rounded to the nearest multiple of 25 milliseconds.
     *
     * This value is based on recently observed application-layer RTT
     * measurements across recently active connections. It excludes
     * connections made to a private address space. If no recent
     * measurement data is available, the value is based on the
     * properties of the underlying connection technology.
     *
     * @var int<0, max>
     */
    public int $rtt {
        /**
         * @throws NetworkNotReadyException in case the API is not ready
         * @throws NetworkNotAvailableException in case the API is not available
         */
        get;
    }

    /**
     * Returns {@see true} if the user has set a
     * reduced data usage option.
     */
    public bool $savingTraffic {
        /**
         * @throws NetworkNotReadyException in case the API is not ready
         * @throws NetworkNotAvailableException in case the API is not available
         */
        get;
    }

    /**
     * Gets the type of connection a device is using
     * to communicate with the network.
     *
     * If the network type cannot be determined, the value will be indicated
     * as {@see NetworkType::Other}. If there is no internet connection,
     * the value corresponds to {@see NetworkType::None}.
     */
    public NetworkType $type {
        /**
         * @throws NetworkNotReadyException in case the API is not ready
         * @throws NetworkNotAvailableException in case the API is not available
         */
        get;
    }
}
