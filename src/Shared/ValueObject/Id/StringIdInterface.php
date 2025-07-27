<?php

declare(strict_types=1);

namespace Boson\Shared\ValueObject\Id;

use Boson\Contracts\ValueObject\StringValueObjectInterface;

/**
 * Representation of all string-like identifiers.
 */
interface StringIdInterface extends
    StringValueObjectInterface,
    IdInterface {}
