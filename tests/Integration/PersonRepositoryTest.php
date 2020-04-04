<?php

namespace LaravelQueryFactory\Tests\Integration;

use LaravelQueryFactory\Facades\QueryFactoryFacade;
use Illuminate\Database\Query\Builder as QueryBuilder;
use LaravelQueryFactory\Traits\MockQueryFactory;
use Mockery;

class PersonRepositoryTest extends TestCase
{
    use MockQueryFactory;

    public function testFindWithGeneratedSql()
    {
        $connection = $this->mockConnection('mysql');
        $queryBuilder = Person::newQueryBuilder();
        $queryBuilder->connection = $connection;
        QueryFactoryFacade::shouldReceive('createQueryBuilder')->andReturn($queryBuilder);

        $repository = new PersonRepository();
        $repository->findById(1);
        $this->assertSame('select * from `people` where `people`.`id` = ? limit 1', $queryBuilder->toSql());
        $this->assertSame([1], $queryBuilder->getBindings());
    }

    public function testFindWithResult()
    {
        $connection = $this->mockConnection('mysql');

        // Mock result from connection
        $connection->shouldReceive('select')->once()->andReturn([['id' => 1]]);

        $queryBuilder = Person::newQueryBuilder();
        $queryBuilder->connection = $connection;
        QueryFactoryFacade::shouldReceive('createQueryBuilder')->andReturn($queryBuilder);

        $repository = new PersonRepository();
        $person = $repository->findById(1);
        $this->assertInstanceOf(Person::class, $person);
    }

    public function testFindByGenderWithMockingQueryBuilder()
    {
        $connection = $this->mockConnection('mysql');

        // Mocking Query Builder
        $queryBuilder = Mockery::mock(QueryBuilder::class);
        $queryBuilder->shouldReceive('getConnection')->andReturn($connection);
        $queryBuilder->shouldReceive('from')->with('people')->andReturnSelf();
        $queryBuilder->shouldReceive('where')->once()->with('gender', '=', 'male')->andReturnSelf();
        $queryBuilder->shouldReceive('get')->once()->with(['*'])->andReturnSelf();
        $queryBuilder->shouldReceive('all')
            ->once()
            ->withNoArgs()
            ->andReturn([['id' => 1, 'gender' => 'male']]);

        QueryFactoryFacade::shouldReceive('createQueryBuilder')->andReturn($queryBuilder);

        $repository = new PersonRepository();
        $persons = $repository->findByGender('male');
        $this->assertSame(1, $persons->first()->id);
    }
}