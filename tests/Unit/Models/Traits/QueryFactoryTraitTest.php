<?php

namespace LaravelQueryFactory\Tests\Unit\Models\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use LaravelQueryFactory\Facades\QueryFactoryFacade;
use LaravelQueryFactory\Models\Traits\QueryFactoryTrait;
use LaravelQueryFactory\QueryFactory;
use LaravelQueryFactory\Tests\Unit\TestCase;
use Mockery;

class QueryFactoryTraitTest extends TestCase
{
    public function tearDown()
    {
        QueryFactoryFacade::setFacadeApplication(null);
    }

    public function testSetGetQueryFactory()
    {
        $queryFactory = new QueryFactory();
        $mock = $this->mockTrait()->setQueryFactory($queryFactory);
        $this->assertSame($queryFactory, $mock->getQueryFactory());
    }

    public function testGetQueryFactory()
    {
        $app = ['query-factory' => QueryFactory::class];
        QueryFactoryFacade::setFacadeApplication($app);
        $this->assertInstanceOf(QueryFactory::class, $this->mockTrait()->getQueryFactory());
    }

    public function testNewQueryBuilder()
    {
        $app = ['query-factory' => QueryFactory::class];
        QueryFactoryFacade::setFacadeApplication($app);
        $connection = Mockery::mock(Connection::class)->makePartial();
        $queryBuilder = call_user_func([$this->mockTrait(), 'newQueryBuilder'], $connection);
        $this->assertInstanceOf(Builder::class, $queryBuilder);
    }

    private function mockTrait()
    {
        return Mockery::mock(QueryFactoryTrait::class)->makePartial();
    }
}