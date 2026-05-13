<?php

declare(strict_types=1);

namespace Mr3od\Textbee\Tests;

use Mr3od\Textbee\TextbeeProvider;
use Orchestra\Testbench\TestCase;

class IntegrationTestCase extends TestCase
{
    protected static $latestResponse;

    protected function getPackageProviders($app)
    {
        return [TextbeeProvider::class];
    }
}
