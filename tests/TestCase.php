<?php

namespace QueueMaster\Reporter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use QueueMaster\Reporter\QueueMasterReporterServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            QueueMasterReporterServiceProvider::class,
        ];
    }
}
