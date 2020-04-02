<?php

namespace LaravelQueryFactory\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use LaravelQueryFactory\QueryFactoryProvider;

class TestCase extends BaseTestCase
{
    /**
     * @inheritDoc
     */
    public function createApplication()
    {
        $this->app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $this->app->make(Kernel::class)->bootstrap();
        $this->app->register(QueryFactoryProvider::class);

        return $this->app;
    }
}