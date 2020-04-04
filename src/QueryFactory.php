<?php

namespace LaravelQueryFactory;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class QueryFactory
{
    public function createEloquentQueryBuilder(Model $model, ?Connection $connection = null): EloquentQueryBuilder
    {
        $queryBuilder = new EloquentQueryBuilder($this->createQueryBuilder($connection ?? $model->getConnection()));
        $queryBuilder->setModel($model);

        return $queryBuilder;
    }

    public function createQueryBuilder(Connection $connection): QueryBuilder
    {
        return $connection->query();
    }
}