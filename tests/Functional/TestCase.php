<?php

declare(strict_types=1);

namespace Boson\Tests\Functional;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('boson-php/runtime')]
abstract class TestCase extends BaseTestCase {}
