<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

interface ScriptsExtensionInterface extends
    MutableScriptsSetInterface,
    ScriptEvaluatorInterface {}
