<?php

namespace LaravelQueryFactory\Traits;

use LaravelQueryFactory\QueryFactory;
use LaravelQueryFactory\Facades\QueryFactoryFacade;

trait QueryFactoryTrait
{
    private $queryFactory;

    public function setQueryFactory(QueryFactory $queryFactory): self
    {
        $this->queryFactory = $queryFactory;

        return $this;
    }

    public function getQueryFactory(): QueryFactory
    {
        if ($this->queryFactory === null) {
            return QueryFactoryFacade::getFacadeRoot();
        }

        return $this->queryFactory;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newEloquentBuilder($query)
    {
        return $this->getQueryFactory()->createEloquentQueryBuilder($this, $this->getConnection());
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        return $this->getQueryFactory()->createQueryBuilder($this->getConnection());
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    abstract public function getConnection();
}