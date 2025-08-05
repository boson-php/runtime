<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Application;

use Boson\ApplicationCreateInfo;
use Boson\Tests\Stub\TestingApplicationStub;
use Boson\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/runtime')]
abstract class ApplicationTestCase extends TestCase
{
    protected function createApplication(ApplicationCreateInfo $info = new ApplicationCreateInfo()): TestingApplicationStub
    {
        return new TestingApplicationStub($info);
    }
}
