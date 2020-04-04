<?php

namespace LaravelQueryFactory\Tests\Integration;

use Illuminate\Database\Eloquent\Model;
use LaravelQueryFactory\Models\Traits\QueryFactoryTrait;

class Person extends Model
{
    use QueryFactoryTrait;

    protected $fillable = [
        'name',
        'gender',
    ];
}