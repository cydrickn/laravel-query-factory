# Laravel Query Factory

Laravel Query Factory, is library for you to  easily mock your
query builder without sending query from real database.

The library is to use for developers who really follow the Automating Testing,
The unit test is the most low level where you need tto test your whole system.

### Problem want to solve by this Library

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

### TODO

* [ ] Add on How to use Laravel Query Factory in README.md
* [ ] Create Unit Test
* [ ] Create MockConnectionC