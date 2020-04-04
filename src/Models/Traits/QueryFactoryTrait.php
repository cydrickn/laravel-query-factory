<?php

namespace LaravelQueryFactory\Models\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use LaravelQueryFactory\QueryFactory;
use LaravelQueryFactory\Facades\QueryFactoryFacade;

trait QueryFactoryTrait
{
    /**
     * @var \LaravelQueryFactory\QueryFactory
     */
    private $queryFactory;

    public static function newQueryBuilder(?Connection $connection = null): Builder
    {
        return (new static)->newBaseQueryBuilder($connection);
    }

    public function setQueryFactory(QueryFactory $queryFactory): self
    {
        $this->queryFactory = $queryFactory;

        return $this;
    }

    public function getQueryFactory(): QueryFactory
    {
        if ($this->queryFactory instanceof QueryFactory) {
            return $this->queryFactory;
        }

        return QueryFactoryFacade::getFacadeRoot();
    }

    /**
     * @param \Illuminate\Database\Connection|null $connection
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder(?Connection $connection = null)
    {
        return $this->getQueryFactory()->createQueryBuilder($connection ?? $this->getConnection());
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    abstract public function getConnection();
}