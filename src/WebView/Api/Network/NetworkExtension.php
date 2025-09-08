<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network;

use Boson\Dispatcher\EventListener;
use Boson\WebView\Api\Bindings\BindingsExtensionInterface;
use Boson\WebView\Api\Data\DataExtensionInterface;
use Boson\WebView\Api\Data\Exception\WebViewIsNotReadyException;
use Boson\WebView\Api\Network\Event\NetworkInfoChanged;
use Boson\WebView\Api\Network\Exception\NetworkNotAvailableException;
use Boson\WebView\Api\Network\Exception\NetworkNotReadyException;
use Boson\WebView\Api\Scripts\ScriptsExtensionInterface;
use Boson\WebView\Api\WebViewExtension;
use Boson\WebView\WebView;

/**
 * @phpstan-type NetworkInfoType array{
 *     downlink: float|int,
 *     downlinkMax: float|int|null,
 *     effectiveType: non-empty-string,
 *     rtt: float|int,
 *     saveData: bool,
 *     type: non-empty-string,
 * }
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class NetworkExtension extends WebViewExtension implements
    NetworkExtensionInterface
{
    /**
     * @var NetworkInfoType
     */
    private ?array $clientNetworkInfo = null {
        /**
         * @return NetworkInfoType
         */
        get => match (true) {
            $this->clientNetworkInfo === null => $this->clientNetworkInfo = $this->fetchClientInfo(),
            $this->isEventsEnabled => $this->clientNetworkInfo,
            default => $this->fetchClientInfo(),
        };
    }

    public float $downlink {
        /** @phpstan-ignore-next-line : data can never be null */
        get => \max(0, (float) $this->clientNetworkInfo['downlink']);
    }

    public ?float $downlinkMax {
        get {
            /** @phpstan-ignore-next-line : data can never be null */
            $downlinkMax = $this->clientNetworkInfo['downlinkMax'];

            if (\is_float($downlinkMax)) {
                return \max($downlinkMax, $this->downlink);
            }

            return null;
        }
    }

    public NetworkEffectiveType $effectiveType {
        get => $this->downlink === 0.0
            ? NetworkEffectiveType::None
            /** @phpstan-ignore-next-line : data can never be null */
            : match ($this->clientNetworkInfo['effectiveType']) {
                'slow-2g' => NetworkEffectiveType::VerySlow,
                '2g' => NetworkEffectiveType::Slow,
                '3g' => NetworkEffectiveType::Medium,
                '4g' => NetworkEffectiveType::Fast,
                default => NetworkEffectiveType::Other,
            };
    }

    public int $rtt {
        /** @phpstan-ignore-next-line : data can never be null */
        get => \max(0, (int) $this->clientNetworkInfo['rtt']);
    }

    public bool $savingTraffic {
        /** @phpstan-ignore-next-line : data can never be null */
        get => (bool) $this->clientNetworkInfo['saveData'];
    }

    public NetworkType $type {
        /** @phpstan-ignore-next-line : data can never be null */
        get => match ($this->clientNetworkInfo['type']) {
            'bluetooth' => NetworkType::Bluetooth,
            'cellular' => NetworkType::Cellular,
            'ethernet' => NetworkType::Ethernet,
            'wifi' => NetworkType::WiFi,
            'wimax' => NetworkType::WiMax,
            default => $this->downlink === 0.0
                ? NetworkType::None
                : NetworkType::Other,
        };
    }

    /**
     * Whether to enable network-related events.
     */
    private readonly bool $isEventsEnabled;

    public function __construct(
        WebView $context,
        EventListener $listener,
        NetworkExtensionCreateInfo $info,
        private readonly BindingsExtensionInterface $bindings,
        private readonly ScriptsExtensionInterface $scripts,
        private readonly DataExtensionInterface $data,
    ) {
        parent::__construct($context, $listener);

        $this->isEventsEnabled = $info->enableEvents;

        if ($this->isEventsEnabled) {
            $this->registerDefaultFunctions();
            $this->registerDefaultClientEventListeners();
        }
    }

    private function registerDefaultFunctions(): void
    {
        $this->bindings->bind('boson.network.onChange', $this->onChange(...));
    }

    /**
     * Registers default event listeners for webview events.
     */
    private function registerDefaultClientEventListeners(): void
    {
        $this->scripts->add(<<<'JS'
            navigator.connection.addEventListener('change', () => boson.network.onChange());
            JS);
    }

    private function onChange(): void
    {
        $this->flushClientInfo();

        $this->dispatch(new NetworkInfoChanged(
            subject: $this->webview,
        ));
    }

    private function flushClientInfo(): void
    {
        $this->clientNetworkInfo = null;
    }

    /**
     * @return NetworkInfoType
     */
    private function fetchClientInfo(): array
    {
        try {
            if ($this->data->get('navigator.connection instanceof NetworkInformation') !== true) {
                throw NetworkNotAvailableException::becauseNetworkNotAvailable();
            }
        } catch (WebViewIsNotReadyException) {
            throw NetworkNotReadyException::becauseNetworkNotReady();
        }

        /** @var NetworkInfoType */
        return $this->data->get('{
            downlink: navigator.connection.downlink ?? 0.0,
            downlinkMax: navigator.connection.downlinkMax ?? null,
            effectiveType: navigator.connection.effectiveType ?? "4g",
            rtt: navigator.connection.rtt ?? 0,
            saveData: navigator.connection.saveData ?? false,
            type: navigator.connection.type ?? "unknown",
        }');
    }
}
