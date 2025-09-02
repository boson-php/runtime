<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Bindings;

/**
 * Manages the binding between PHP callbacks and JavaScript functions.
 *
 * Provides functionality to create and manage JavaScript functions
 * that are bound to PHP callbacks. It handles the registration, execution,
 * and cleanup of these bindings, as well as the communication between
 * JavaScript and PHP through a message-based RPC system.
 */
interface BindingsExtensionInterface extends
    BindingsMapInterface,
    MutableBindingsMapInterface {}
