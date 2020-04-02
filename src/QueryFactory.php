<?php

namespace LaravelQueryFactory;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class QueryFactory
{
    public function createEloquentQueryBuilder(Model $model, Connection $connection): EloquentQueryBuilder
    {
        return $model->newEloquentBuilder($this->createQueryBuilder($connection));
    }

    public function createQueryBuilder(Connection $connection): QueryBuilder
    {
        return $connection->query();
    }
}