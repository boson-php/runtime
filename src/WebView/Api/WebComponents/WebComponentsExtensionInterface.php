<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents;

/**
 * Allows to register custom web components, check their existence,
 * and get their count.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements
 */
interface WebComponentsExtensionInterface extends
    MutableWebComponentsMapInterface {}
