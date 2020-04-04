<?php

namespace LaravelQueryFactory\Facades;

use Illuminate\Support\Facades\Facade;

class QueryFactoryFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'query-factory';
    }
}