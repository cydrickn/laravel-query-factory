<?php

namespace LaravelQueryFactory\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelQueryFactory\Traits\QueryFactoryTrait;

class Person extends Model
{
    use QueryFactoryTrait;

    protected $fillable = [
        'id',
        'name',
        'birthDate',
        'gender',
    ];
}