<?php

namespace LaravelQueryFactory\Tests\Unit\Facacdes;

use LaravelQueryFactory\Facades\QueryFactoryFacade;
use LaravelQueryFactory\QueryFactory;
use LaravelQueryFactory\Tests\Unit\TestCase;

class QueryFactoryFacadeTest extends TestCase
{
    public function tearDown(): void
    {
        QueryFactoryFacade::setFacadeApplication(null);
    }

    public function testGetFacadeAccessor()
    {
        $queryFactory = new QueryFactory();
        $app = ['query-factory' => $queryFactory];
        QueryFactoryFacade::setFacadeApplication($app);
        $this->assertSame($queryFactory, QueryFactoryFacade::getFacadeRoot());
    }
}