<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

use Boson\Api\ApplicationExtension;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\Dialog
 */
final class DialogExtension extends ApplicationExtension implements DialogExtensionInterface
{
    protected CData $ptr {
        /** @phpstan-ignore-next-line : PHPStan does not support property inheritance */
        get => $this->app->saucer->saucer_desktop_new(parent::$ptr::get());
    }

    private function applyDirectory(CData $options, ?string $directory): void
    {
        $directory ??= \getcwd();

        if (\is_string($directory) && $directory !== '') {
            $this->app->saucer->saucer_picker_options_set_initial($options, $directory);
        }
    }

    /**
     * @param iterable<mixed, mixed> $filter
     */
    private function applyFilter(CData $options, iterable $filter): void
    {
        $index = 0;

        foreach ($filter as $item) {
            ++$index;

            if (\is_string($item) && $item !== '') {
                $this->app->saucer->saucer_picker_options_add_filter($options, $item);
                continue;
            }

            throw new \InvalidArgumentException(\sprintf(
                'Filter #%d element must be a non empty string',
                $index - 1,
            ));
        }
    }

    /**
     * @param iterable<mixed, mixed> $filter
     */
    private function createOptions(?string $directory, iterable $filter): CData
    {
        $options = $this->app->saucer->saucer_picker_options_new();

        $this->applyDirectory($options, $directory);
        $this->applyFilter($options, $filter);

        return $options;
    }

    /**
     * @param iterable<mixed, mixed> $filter
     * @param \Closure(CData, CData): ?CData $selector
     *
     * @return non-empty-string|null
     */
    private function selectOne(?string $directory, iterable $filter, \Closure $selector): ?string
    {
        $options = $this->createOptions($directory, $filter);

        try {
            $pointer = $selector($this->ptr, $options);

            if ($pointer === null || \FFI::isNull($pointer)) {
                return null;
            }

            $result = \FFI::string($pointer);

            return $result === '' ? null : $result;
        } finally {
            $this->app->saucer->saucer_picker_options_free($options);
        }
    }

    /**
     * @param iterable<mixed, mixed> $filter
     * @param \Closure(CData, CData): ?CData $selector
     *
     * @return list<non-empty-string>
     */
    private function selectMany(?string $directory, iterable $filter, \Closure $selector): array
    {
        $options = $this->createOptions($directory, $filter);

        $result = [];
        try {
            $pointer = $selector($this->ptr, $options);

            if ($pointer === null || \FFI::isNull($pointer)) {
                return [];
            }

            /** @phpstan-ignore-next-line : The $pointer[$i] is CData|null */
            for ($i = 0; !\FFI::isNull($pointer[$i]); ++$i) {
                /** @phpstan-ignore-next-line : The $pointer[$i] is CData */
                $item = \FFI::string($pointer[$i]);

                if ($item !== '') {
                    $result[] = $item;
                }
            }

            return $result;
        } finally {
            $this->app->saucer->saucer_picker_options_free($options);
        }
    }

    public function open(string $url): void
    {
        $this->app->saucer->saucer_desktop_open($this->ptr, $url);
    }

    public function selectDirectory(?string $directory = null, iterable $filter = []): ?string
    {
        return $this->selectOne($directory, $filter, $this->app->saucer->saucer_desktop_pick_folder(...));
    }

    public function selectFile(?string $directory = null, iterable $filter = []): ?string
    {
        return $this->selectOne($directory, $filter, $this->app->saucer->saucer_desktop_pick_file(...));
    }

    /**
     * @return list<non-empty-string>
     */
    public function selectFiles(?string $directory = null, iterable $filter = []): array
    {
        return $this->selectMany($directory, $filter, $this->app->saucer->saucer_desktop_pick_files(...));
    }

    /**
     * @return list<non-empty-string>
     */
    public function selectDirectories(?string $directory = null, iterable $filter = []): array
    {
        return $this->selectMany($directory, $filter, $this->app->saucer->saucer_desktop_pick_folders(...));
    }

    public function __destruct()
    {
        $this->app->saucer->saucer_desktop_free($this->ptr);
    }
}
