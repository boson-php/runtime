<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Network;

enum NetworkEffectiveType
{
    case None;
    case VerySlow;
    case Slow;
    case Medium;
    case Fast;
    case Other;
}
