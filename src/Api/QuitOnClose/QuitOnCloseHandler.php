<?php

declare(strict_types=1);

namespace Boson\Api\QuitOnClose;

use Boson\Application;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Contracts\EventListener\Subscription\CancellableSubscriptionInterface;
use Boson\Window\Event\WindowClosed;

/**
 * Controls application shutdown behavior when all windows are closed.
 *
 * This extension subscribes to {@see WindowClosed} events and invokes
 * {@see Application::quit()} when no windows remain and the `quitOnClose`
 * feature is enabled in the application info.
 */
final readonly class QuitOnCloseHandler
{
    private CancellableSubscriptionInterface $subscription;

    /**
     * Initialize the extension and register event listeners.
     *
     * @param Application $app application instance whose lifecycle can be
     *        controlled
     * @param EventListenerInterface $listener event listener used to
     *        subscribe to window events
     */
    public function __construct(
        private Application $app,
        EventListenerInterface $listener,
    ) {
        $this->subscription = $this->listenEvents($listener);
    }

    /**
     * Subscribe to window close events if the feature is enabled.
     */
    private function listenEvents(EventListenerInterface $listener): CancellableSubscriptionInterface
    {
        return $listener->addEventListener(WindowClosed::class, $this->onWindowClose(...));
    }

    /**
     * Check whether the application should quit.
     *
     * @return bool {@see true} when there are no open windows;
     *              {@see false} otherwise
     */
    private function shouldQuit(): bool
    {
        return $this->app->windows->count() === 0;
    }

    /**
     * Handle window close: quit when no windows remain.
     */
    private function onWindowClose(): void
    {
        $this->subscription->cancel();

        if (!$this->shouldQuit()) {
            return;
        }

        $this->app->quit();
    }
}
