<?php

namespace LaravelQueryFactory;

use Illuminate\Support\ServiceProvider;

class QueryFactoryProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('query-factory', function () {
            return new QueryFactory();
        });
    }
}