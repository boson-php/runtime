<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery;

use Boson\Dispatcher\EventListener;
use Boson\Shared\Marker\ExpectsSecurityContext;
use Boson\WebView\Api\Battery\Event\BatteryChargingStateChanged;
use Boson\WebView\Api\Battery\Event\BatteryChargingTimeChanged;
use Boson\WebView\Api\Battery\Event\BatteryDischargingTimeChanged;
use Boson\WebView\Api\Battery\Event\BatteryLevelChanged;
use Boson\WebView\Api\Battery\Exception\BatteryNotAvailableException;
use Boson\WebView\Api\Battery\Exception\BatteryNotReadyException;
use Boson\WebView\Api\Battery\Exception\InsecureBatteryContextException;
use Boson\WebView\Api\BatteryApiInterface;
use Boson\WebView\Api\Data\Exception\WebViewIsNotReadyException;
use Boson\WebView\Api\WebViewExtension;
use Boson\WebView\WebView;

/**
 * @phpstan-type BatteryInfoType array{
 *     level: float|int<0, 1>,
 *     charging: bool,
 *     chargingTime: float|int<0, max>,
 *     dischargingTime: float|int<0, max>|null,
 * }
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 *
 * @uses \Boson\WebView\Api\DataApiInterface
 * @uses \Boson\WebView\Api\BindingsApiInterface
 * @uses \Boson\WebView\Api\SecurityApiInterface
 * @uses \Boson\WebView\Api\ScriptsApiInterface
 */
#[ExpectsSecurityContext]
final class BatteryApi extends WebViewExtension implements BatteryApiInterface
{
    public float $level {
        /** @phpstan-ignore-next-line : data can never be null */
        get => (float) $this->data['level'];
    }

    public bool $isCharging {
        /** @phpstan-ignore-next-line : data can never be null */
        get => $this->data['charging'];
    }

    public int $chargingTime {
        /** @phpstan-ignore-next-line : data can never be null */
        get => (int) $this->data['chargingTime'];
    }

    public ?int $dischargingTime {
        get => match (true) {
            /** @phpstan-ignore-next-line : data can never be null */
            $this->data['dischargingTime'] === null => null,
            default => (int) $this->data['dischargingTime'],
        };
    }

    /**
     * @var BatteryInfoType
     */
    private ?array $data = null {
        get => match (true) {
            $this->data === null => $this->data = $this->fetchClientInfo(),
            $this->isEventsEnabled => $this->data,
            default => $this->fetchClientInfo(),
        };
    }

    /**
     * Whether to enable battery-related events.
     */
    private readonly bool $isEventsEnabled;

    public function __construct(WebView $context, EventListener $listener)
    {
        parent::__construct($context, $listener);

        $this->isEventsEnabled = $this->webview->info->battery->enableEvents;

        if ($this->isEventsEnabled) {
            $this->registerDefaultFunctions();
            $this->registerDefaultClientEventListeners();
        }
    }

    private function registerDefaultFunctions(): void
    {
        $this->webview->bindings->bind('boson.battery.onLevelChange', $this->onLevelChange(...));
        $this->webview->bindings->bind('boson.battery.onChargingChange', $this->onChargingChange(...));
        $this->webview->bindings->bind('boson.battery.onChargingTimeChange', $this->onChargingTimeChange(...));
        $this->webview->bindings->bind('boson.battery.onDischargingTimeChange', $this->onDischargingTimeChange(...));
    }

    /**
     * Registers default event listeners for webview events.
     */
    private function registerDefaultClientEventListeners(): void
    {
        $this->webview->scripts->add(<<<'JS'
            document.addEventListener('levelchange', () => boson.battery.onLevelChange());
            document.addEventListener('chargingchange', () => boson.battery.onChargingChange());
            document.addEventListener('chargingtimechange', () => boson.battery.onChargingTimeChange());
            document.addEventListener('dischargingtimechange', () => boson.battery.onDischargingTimeChange());
            JS);
    }

    private function onLevelChange(): void
    {
        $this->flushClientInfo();

        $this->dispatch(new BatteryLevelChanged(
            subject: $this->webview,
            level: $this->level,
        ));
    }

    private function onChargingChange(): void
    {
        $this->flushClientInfo();

        $this->dispatch(new BatteryChargingStateChanged(
            subject: $this->webview,
            isCharging: $this->isCharging,
        ));
    }

    private function onChargingTimeChange(): void
    {
        $this->flushClientInfo();

        $this->dispatch(new BatteryChargingTimeChanged(
            subject: $this->webview,
            chargingTime: $this->chargingTime,
        ));
    }

    private function onDischargingTimeChange(): void
    {
        $this->flushClientInfo();

        $this->dispatch(new BatteryDischargingTimeChanged(
            subject: $this->webview,
            dischargingTime: $this->dischargingTime,
        ));
    }

    private function flushClientInfo(): void
    {
        $this->data = null;
    }

    /**
     * @return BatteryInfoType
     */
    private function fetchClientInfo(): array
    {
        if (!$this->webview->security->isSecureContext) {
            throw InsecureBatteryContextException::becauseContextIsInsecure();
        }

        try {
            if ($this->webview->data->get('navigator.getBattery instanceof Function') !== true) {
                throw BatteryNotAvailableException::becauseBatteryNotAvailable();
            }
        } catch (WebViewIsNotReadyException $e) {
            throw BatteryNotReadyException::becauseBatteryNotReady($e);
        }

        /** @var BatteryInfoType */
        return $this->webview->data->get('navigator.getBattery()
            .then((manager) => ({
                level: manager.level,
                charging: manager.charging,
                chargingTime: manager.chargingTime,
                dischargingTime: manager.dischargingTime,
            })) || {}');
    }
}
