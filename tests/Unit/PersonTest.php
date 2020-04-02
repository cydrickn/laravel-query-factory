<?php

namespace LaravelQueryFactory\Tests\Unit;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use LaravelQueryFactory\Facades\QueryFactoryFacade;
use LaravelQueryFactory\Tests\Models\Person;
use LaravelQueryFactory\Tests\TestCase;
use LaravelQueryFactory\Tests\Traits\MockQueryFactor;
use Mockery;

class PersonTest extends TestCase
{
    use MockQueryFactor;

    public function testFindWithRefuse()
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage('SQLSTATE[HY000] [2002] Connection refused');
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
        $queryBuilder = Person::getQueryBuilder();
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
        $queryBuilder = Person::getQueryBuilder();
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
}