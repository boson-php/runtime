<?php

declare(strict_types=1);

namespace Boson {

    use Boson\Api\CentralProcessor\CentralProcessorInfoInterface;
    use Boson\Api\Dialog\DialogApiInterface;
    use Boson\Api\OperatingSystem\OperatingSystemInfoInterface;

    class Application
    {
        /**
         * Gets access to the Dialog API of the application.
         */
        public readonly DialogApiInterface $dialog;

        /**
         * Gets access to the CPU Information API of the application.
         */
        public readonly CentralProcessorInfoInterface $cpu;

        /**
         * Gets access to the OS Information API of the application.
         */
        public readonly OperatingSystemInfoInterface $os;
    }

}

namespace Boson\Window {

    class Window
    {
    }

}


namespace Boson\WebView {

    use Boson\WebView\Api\Bindings\BindingsApiInterface;
    use Boson\WebView\Api\Data\DataRetrieverInterface;
    use Boson\WebView\Api\Schemes\SchemesProviderInterface;
    use Boson\WebView\Api\Scripts\ScriptsApiInterface;
    use Boson\WebView\Api\Security\SecurityInfoInterface;

    class WebView
    {
        /**
         * Gets access to the Bindings API of the webview.
         *
         * Provides the ability to register PHP functions
         * in the webview.
         */
        public readonly BindingsApiInterface $bindings;

        /**
         * Gets access to the Data API of the webview.
         *
         * Provides the ability to receive variant data from
         * the current document.
         */
        public readonly DataRetrieverInterface $data;

        /**
         * Gets access to the Schemes API of the webview.
         */
        public readonly SchemesProviderInterface $schemes;

        /**
         * Gets access to the Scripts API of the webview.
         *
         * Provides the ability to register a JavaScript code
         * in the webview.
         */
        public readonly ScriptsApiInterface $scripts;

        /**
         * Gets access to the Security API of the webview.
         */
        public readonly SecurityInfoInterface $security;

    }

}
