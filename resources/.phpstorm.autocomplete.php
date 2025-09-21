<?php

declare(strict_types=1);

namespace Boson {

    use Boson\Api\CentralProcessor\CentralProcessorExtensionInterface;
    use Boson\Api\Dialog\DialogExtensionInterface;
    use Boson\Api\Alert\AlertExtensionInterface;
    use Boson\Api\OperatingSystem\OperatingSystemExtensionInterface;

    class Application
    {
        /**
         * Gets access to the Dialog API of the application.
         */
        public readonly DialogExtensionInterface $dialog;

        /**
         * Gets access to the CPU Information API of the application.
         */
        public readonly CentralProcessorExtensionInterface $cpu;

        /**
         * Gets access to the OS Information API of the application.
         */
        public readonly OperatingSystemExtensionInterface $os;
    }

}

namespace Boson\Window {

    class Window
    {
    }

}


namespace Boson\WebView {

    use Boson\WebView\Api\Bindings\BindingsExtensionInterface;
    use Boson\WebView\Api\Data\DataExtensionInterface;
    use Boson\WebView\Api\Schemes\SchemesExtensionInterface;
    use Boson\WebView\Api\Scripts\ScriptsExtensionInterface;
    use Boson\WebView\Api\Security\SecurityExtensionInterface;
    use Boson\WebView\Api\WebComponents\WebComponentsExtensionInterface;

    class WebView
    {
        /**
         * Gets access to the Bindings API of the webview.
         *
         * Provides the ability to register PHP functions
         * in the webview.
         */
        public readonly BindingsExtensionInterface $bindings;

        /**
         * Gets access to the Data API of the webview.
         *
         * Provides the ability to receive variant data from
         * the current document.
         */
        public readonly DataExtensionInterface $data;

        /**
         * Gets access to the Schemes API of the webview.
         */
        public readonly SchemesExtensionInterface $schemes;

        /**
         * Gets access to the Scripts API of the webview.
         *
         * Provides the ability to register a JavaScript code
         * in the webview.
         */
        public readonly ScriptsExtensionInterface $scripts;

        /**
         * Gets access to the Security API of the webview.
         */
        public readonly SecurityExtensionInterface $security;

    }

}
