<?php

namespace LaravelQueryFactory;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class QueryFactory
{
    public function createEloquentQueryBuilderFromName(string $className, ?Connection $connection = null): EloquentQueryBuilder
    {
        $model = new $className;
        $queryBuilder = new EloquentQueryBuilder($this->createQueryBuilder($connection ?? $model->getConnection()));
        $queryBuilder->setModel($model);

        return $queryBuilder;
    }

    public function createEloquentQueryBuilder(Model $model): EloquentQueryBuilder
    {
        $queryBuilder = new EloquentQueryBuilder($this->createQueryBuilder($model->getConnection()));
        $queryBuilder->setModel($model);

        return $queryBuilder;
    }

    public function createQueryBuilder(Connection $connection): QueryBuilder
    {
        return $connection->query();
    }
}