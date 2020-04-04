<?php

namespace LaravelQueryFactory\Tests\Integration;

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