<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network;

enum NetworkType
{
    case None;
    case Bluetooth;
    case Cellular;
    case Ethernet;
    case WiFi;
    case WiMax;
    case Other;
}
