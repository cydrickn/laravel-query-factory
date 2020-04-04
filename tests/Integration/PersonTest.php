<?php

namespace LaravelQueryFactory\Tests\Integration;

use Illuminate\Database\Query\Builder as QueryBuilder;
use LaravelQueryFactory\Facades\QueryFactoryFacade;
use LaravelQueryFactory\QueryFactory;
use LaravelQueryFactory\Traits\MockQueryFactory;
use Mockery;

class PersonTest extends TestCase
{
    use MockQueryFactory;

    public function testFindWithRefuse()
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionCode(2002);
        Person::find(1);
    }

    public function testFindWithMockQueryBuilder()
    {
        $connection = $this->mockConnection('mysql');
        $queryBuilder = Mockery::mock(QueryBuilder::class);
        $queryBuilder->shouldReceive('getConnection')->andReturn($connection);
        $queryBuilder->shouldReceive('from')->with('people')->andReturnSelf();
        $queryBuilder->shouldReceive('where')->once()->with('people.id', '=', 1)->andReturnSelf();
        $queryBuilder->shouldReceive('take')->once()->with(1)->andReturnSelf();
        $queryBuilder->shouldReceive('get')->once()->with(['*'])->andReturnSelf();
        $queryBuilder->shouldReceive('all')
            ->once()
            ->withNoArgs()
            ->andReturn([['id' => 1]]);
        QueryFactoryFacade::shouldReceive('createQueryBuilder')->andReturn($queryBuilder);
        $person = Person::find(1);
        $this->assertSame(1, $person->id);
    }

    public function testFindWithMockConnection()
    {
        $connection = $this->mockConnection('mysql');
        $queryBuilder = Person::newQueryBuilder();
        $queryBuilder->connection = $connection;
        QueryFactoryFacade::shouldReceive('createQueryBuilder')->andReturn($queryBuilder);

        Person::find(1);
        $this->assertSame('select * from `people` where `people`.`id` = ? limit 1', $queryBuilder->toSql());
        $this->assertSame([1], $queryBuilder->getBindings());
    }

    public function testDelete()
    {
        $connection = $this->mockConnection('mysql', ['select']);
        $connection->shouldReceive('select')->once()->andReturn([['id' => 1]]);
        $connection->shouldReceive('delete')->once()->andReturn(1);
        $queryBuilder = Person::newQueryBuilder();
        $queryBuilder->connection = $connection;
        $deleteQuery = $queryBuilder->newQuery();
        QueryFactoryFacade::shouldReceive('createQueryBuilder')->andReturn($queryBuilder, $deleteQuery);

        $person = Person::find(1);
        $person->exists = true;
        $person->delete();
        $this->assertSame(
            'delete from `people` where `people`.`id` = ? limit 1',
            $queryBuilder->getGrammar()->compileDelete($queryBuilder)
        );
        $this->assertFalse($person->exists);
    }

    public function testSetGetQueryBuilder()
    {
        $queryFactory = new QueryFactory();
        $person = new Person();
        $person->setQueryFactory($queryFactory);
        $this->assertSame($queryFactory, $person->getQueryFactory());
    }
}