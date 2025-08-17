<?php

declare(strict_types=1);

namespace Boson\Api\OperatingSystem;

use Boson\Api\ApplicationExtension;
use Boson\Api\OperatingSystemApiInterface;
use Boson\Component\OsInfo\OperatingSystem;
use Boson\Contracts\OsInfo\FamilyInterface;
use Boson\Contracts\OsInfo\OperatingSystemInterface;
use Boson\Contracts\OsInfo\StandardInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final class OperatingSystemApi extends ApplicationExtension implements
    OperatingSystemApiInterface
{
    private OperatingSystemInterface $os {
        get => $this->os ??= OperatingSystem::createFromGlobals();
    }

    public string $name {
        get => $this->os->name;
    }

    public string $version {
        get => $this->os->version;
    }

    public ?string $codename {
        get => $this->os->codename;
    }

    public ?string $edition {
        get => $this->os->edition;
    }

    public FamilyInterface $family {
        get => $this->os->family;
    }

    public iterable $standards {
        get => $this->os->standards;
    }

    public function isSupports(StandardInterface $standard): bool
    {
        return $this->os->isSupports($standard);
    }
}
