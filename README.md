# Laravel Query Factory

[![PHP from Packagist](https://img.shields.io/packagist/php-v/cydrickn/laravel-query-factory.svg)](https://packagist.org/packages/cydrickn/laravel-query-factory)
[![Software License](https://img.shields.io/packagist/l/cydrickn/laravel-query-factory.svg)](/LICENSE)
![ci](https://github.com/cydrickn/laravel-query-factory/workflows/ci/badge.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/cydrickn/laravel-query-factory/badge.svg?branch=master)](https://coveralls.io/github/cydrickn/laravel-query-factory?branch=master)

Laravel Query Factory, is library for you to  easily mock your
query builder without sending query from real database.

The library is to use for developers who really follow the Automating Testing,
The unit test is the most low level where you need tto test your whole system.

### Problem that want to solve by this Library

In laravel is one of the problem, where most of the tutorials and even
in document of laravel, the testing is mostly focusing in integration, api and acceptance.

Example of this test are:
```php
<?php

// Model
use Illuminate\Database\Eloquent\Model;

class Person extends Model {
}

// Repository
use Illuminate\Support\Collection;

class PersonRepository
{
    public function findPerson(int $id): ?Person
    {
        return Person::find(1);
    }
}

// Test

class PersonRepositoryTest extends TestCase
{
    public function testFindPerson()
    {
        factory(Person::class)->create(['id' => 1]);
        $person = $this->app->make(PersonRepository::class)->findPerson(1);
        $this->assertInstanceOf(Person::class, $person);
        $this->assertSame(1, $person->id);
    }
}
```

Fromm our example, this is an **Integration** test, not a unit test. Why?
We will answer that why using the list below:
* Unit test a single unit in your system, this are your method and functions.
* If possible any function going out to your class must be in mock form.
    - this is possible if you doing dependency injection
    - this is possible if you are doing SOLID
* Unit test must not call any 3rd party service or service outside the system.

From this list and from the example, our test was calling a database, if you call find from your model
this will immediately call a query using your connection.

Oops!!!, why not use sqlite, since sqlite is just a file base and can be use without other database?
The answer is dont use sqlite for test to just satisfy your unit test, Why?
* Using sqlite, is still consider outside of your class and can still be consider as 3rd party service.
* Not all sql query can handler my sqlite, each database driver have different syntax, especially if you are using
NoSQL.

### Classes and Traits

|Class|Description|
|-----|-----------|
|LaravelQueryFactory\Facades\QueryFactoryFacade|A class for facade use by laravel and lumen|
|LaravelQueryFactory\QueryFactory|The main service that will generate query builder|
|LaravelQueryFactory\QueryFactoryProvider|Provider that will register the `LaravelQueryFactory\QueryFactory` as `query-factory`|

|Trait|Description|
|-----|-----------|
|LaravelQueryFactory\Models\Traits\QueryFactoryTrait|Trait that can use by the model to replace the default `newBaseQueryBuilder`|
|LaravelQueryFactory\Traits\MockQueryFactory|A trait use for testing, to mock connection and pdo|

### How to use

#### Installing

```bash
composer require cydrickn/laravel-query-factory
```


#### Register the Provider

The configuration can be found to `config/app.php`.

To register your provider, add it to the array:

```php
'providers' => [
    // Other Service Providers

   LaravelQueryFactory\QueryFactoryProvider::class,
];
```

#### Creating Model

Model will just use the `LaravelQueryFactory\Models\Traits\QueryFactoryTrait` this will use the facade or you can also
set the QueryFactory using by calling `setQueryFactory`

Adding `QueryFactoryTrait` to your model, this will replace the current generation of Query Builder
using `QueryFactoryFacade` so that you can mock it.
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelQueryFactory\Models\Traits\QueryFactoryTrait;

class Person extends Model
{
    use QueryFactoryTrait;

    protected $fillable = ['name', 'gender'];
}
```

#### Creating Repository/Service

Creating repository or service is just you normal service.
It's your choice if you will inject `LaravelQueryFactory\QueryFactory`,
but in this example we will not since we use the default QueryFactory from our facades.

Person Repository
```php
<?php

namespace App\Repository;

use App\Models\Person;
use Illuminate\Support\Collection;

class PersonRepository
{
    public function findById(): ?Person
    {
        return Person::find(1);
    }
    
    public function findByGender(string $gender): Collection
    {
        return Person::where('gender', '=', $gender)->get();       
    }   
}
```

#### Creating Unit Test

For our unit test you need to use `LaravelQueryFactory\Traits\MockQueryFactory`.
This trait will mock the connection class use by laravel so that it will not connect to any type of database.

for function `mockConnection` it is accepting:
- mysql
- postgres
- sqlite
- sqlserver

Why we need to specify this drivers? so that you can get the generated query for test, since every driver has
different queries on how they convert your query builder.
But I suggest that you use the driver use by your system, so that you can really check your expectation sql.

```php
<?php

namespace App\Tests\Unit;

use LaravelQueryFactory\Traits\MockQueryFactory;
use LaravelQueryFactory\Facades\QueryFactoryFacade;

class PersonRepositoryTest extends TestCase
{
    use MockQueryFactory;

    /**
     * Test by just using mock connection and QueryFacades
     * and you can assert the generated sql.
     */
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

    /**
     * Test by mocking connection with result and QueryFacades
     */
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

    /**
     * Test by mocking query builder
     */
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
```

### Conclusion

Using this library (Laravel Query Factory) you can now mock your query builders.
By mocking the connection will mock the connection so that it will not connect to database.



