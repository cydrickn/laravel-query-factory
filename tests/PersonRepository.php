<?php

namespace LaravelQueryFactory\Tests;

class PersonRepository
{
    public function findPerson(int $id): ?Person
    {
        return Person::find($id);
    }
}