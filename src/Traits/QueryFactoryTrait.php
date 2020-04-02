<?php

namespace LaravelQueryFactory\Traits;

use Illuminate\Database\Query\Builder;
use LaravelQueryFactory\QueryFactory;
use LaravelQueryFactory\Facades\QueryFactoryFacade;

trait QueryFactoryTrait
{
    /**
     * @var \LaravelQueryFactory\QueryFactory
     */
    private $queryFactory;

    public static function getQueryBuilder(): Builder
    {
        return (new static)->newBaseQueryBuilder();
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