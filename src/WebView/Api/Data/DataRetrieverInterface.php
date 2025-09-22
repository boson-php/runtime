<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data;

/**
 * Managing data between PHP and JavaScript in the WebView.
 *
 * Defines the contract for sending JavaScript code to the WebView
 * and receiving responses.
 */
interface DataRetrieverInterface extends
    SyncDataRetrieverInterface,
    AsyncDataRetrieverInterface {}
