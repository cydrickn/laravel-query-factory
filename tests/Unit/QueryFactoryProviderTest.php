<?php

namespace LaravelQueryFactory\Tests\Unit;

use Illuminate\Foundation\Application;
use LaravelQueryFactory\QueryFactory;
use LaravelQueryFactory\QueryFactoryProvider;

class QueryFactoryProviderTest extends TestCase
{
    public function testRegister()
    {
        $app = new Application();
        $provider = new QueryFactoryProvider($app);
        $provider->register();
        $queryFactory = $app->make('query-factory');
        $this->assertInstanceOf(QueryFactory::class, $queryFactory);
    }
}