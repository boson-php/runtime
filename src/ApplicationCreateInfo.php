<?php

declare(strict_types=1);

namespace Boson;

use Boson\Api\Autorun\AutorunExtensionProvider;
use Boson\Api\CentralProcessor\CentralProcessorExtensionProvider;
use Boson\Api\Console\ConsoleExtensionProvider;
use Boson\Api\Dialog\DialogExtensionProvider;
use Boson\Api\OperatingSystem\OperatingSystemExtensionProvider;
use Boson\Api\QuitHandler\QuitHandlerExtensionProvider;
use Boson\Api\QuitOnClose\QuitOnCloseExtensionProvider;
use Boson\Extension\ExtensionProviderInterface;
use Boson\Window\WindowCreateInfo;

//
// Note:
// 1) This "$_" assign hack removes these constants from IDE autocomplete.
// 2) Only define-like constants allows object instances.
//
\define($_ = 'Boson\DEFAULT_APPLICATION_EXTENSIONS', [
    new CentralProcessorExtensionProvider(),
    new OperatingSystemExtensionProvider(),
    new DialogExtensionProvider(),
    new QuitOnCloseExtensionProvider(),
    new ConsoleExtensionProvider(),
    new QuitHandlerExtensionProvider(),
    new AutorunExtensionProvider(),
]);

/**
 * Information (configuration) DTO for creating a new application.
 */
final readonly class ApplicationCreateInfo
{
    /**
     * @var list<ExtensionProviderInterface<Application>>
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const array DEFAULT_APPLICATION_EXTENSIONS = DEFAULT_APPLICATION_EXTENSIONS;

    /**
     * Contains default application name.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_APPLICATION_NAME = 'boson';

    /**
     * List of protocol (scheme) names that will be
     * intercepted by the application.
     *
     * @var list<non-empty-lowercase-string>
     */
    public array $schemes;

    /**
     * @var list<ExtensionProviderInterface<Application>>
     */
    public array $extensions;

    /**
     * @param iterable<mixed, non-empty-string> $schemes list of scheme names
     * @param iterable<mixed, ExtensionProviderInterface<Application>> $extensions
     *        list of enabled application extensions
     */
    public function __construct(
        /**
         * An application optional name.
         *
         * @var non-empty-string
         */
        public string $name = self::DEFAULT_APPLICATION_NAME,
        iterable $schemes = [],
        /**
         * An application threads count.
         *
         * The number of threads will be determined automatically if it
         * is not explicitly specified (defined as {@see null}).
         *
         * @var int<1, max>|null
         */
        public ?int $threads = null,
        /**
         * Automatically detects debug environment if {@see null},
         * otherwise it forcibly enables or disables it.
         */
        public ?bool $debug = null,
        /**
         * Automatically detects the library pathname if {@see null},
         * otherwise it forcibly exposes it.
         *
         * @var non-empty-string|null
         */
        public ?string $library = null,
        /**
         * Automatically terminates the application if
         * all windows have been closed.
         *
         * @deprecated will be removed in future versions it and replaced by
         *             the presence of the {@see QuitOnCloseExtensionProvider}
         *             in the {@see $extensions} list.
         *
         *             To disable this functionality, you should remove the
         *             {@see QuitOnCloseExtensionProvider} from the
         *             {@see $extensions} list, instead of setting the field
         *             to {@see false}.
         */
        public bool $quitOnClose = true,
        /**
         * Automatically starts the application if set to {@see true}.
         *
         * @deprecated will be removed in future versions it and replaced by
         *             the presence of the {@see AutorunExtensionProvider}
         *             in the {@see $extensions} list.
         *
         *             To disable this functionality, you should remove the
         *             {@see AutorunExtensionProvider} from the
         *             {@see $extensions} list, instead of setting the field
         *             to {@see false}.
         */
        public bool $autorun = true,
        iterable $extensions = self::DEFAULT_APPLICATION_EXTENSIONS,
        /**
         * Main (default) window configuration DTO.
         */
        public WindowCreateInfo $window = new WindowCreateInfo(),
    ) {
        $this->schemes = self::schemesToList($schemes);
        $this->extensions = self::extensionsToList($extensions);
    }

    /**
     * @param iterable<mixed, ExtensionProviderInterface<Application>> $extensions
     *
     * @return list<ExtensionProviderInterface<Application>>
     */
    private static function extensionsToList(iterable $extensions): array
    {
        return \iterator_to_array($extensions, false);
    }

    /**
     * @param iterable<mixed, non-empty-string> $schemes
     *
     * @return list<non-empty-lowercase-string>
     */
    private static function schemesToList(iterable $schemes): array
    {
        $result = [];

        foreach ($schemes as $scheme) {
            $result[] = \strtolower($scheme);
        }

        return $result;
    }
}
