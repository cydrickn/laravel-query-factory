<?php

namespace LaravelQueryFactory\Facades;

use Illuminate\Support\Facades\Facade;

class QueryFactoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'query-factory';
    }
}