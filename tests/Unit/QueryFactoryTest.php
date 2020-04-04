<?php

namespace LaravelQueryFactory\Tests\Unit;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use LaravelQueryFactory\QueryFactory;
use Mockery;

class QueryFactoryTest extends TestCase
{
    public function testCreateQueryBuilder()
    {
        $queryFactory = new QueryFactory();
        $connection = Mockery::mock(Connection::class)->makePartial();
        $queryBuilder = $queryFactory->createQueryBuilder($connection);
        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
        $this->assertSame($connection, $queryBuilder->getConnection());
    }

    public function testCreateEloquentQueryBuilder()
    {
        $mockModel = Mockery::mock(Model::class)->makePartial();
        $queryFactory = new QueryFactory();
        $connection = Mockery::mock(Connection::class)->makePartial();
        $eloquentBuilder = $queryFactory->createEloquentQueryBuilder($mockModel, $connection);
        $this->assertInstanceOf(EloquentQueryBuilder::class, $eloquentBuilder);
    }
}