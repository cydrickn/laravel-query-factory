<?php

use LaravelQueryFactory\Tests\Traits;

trait MockConnection
{
    protected function mockConnection(string $driver): ConnectionInterface
    {
        $mockDriverFunction = 'mock' . ucwords($driver);

        return call_user_func([$this, $mockDriverFunction]);
    }

    protected function mockMysql()
    {
    }

    protected function mockPdo()
    {
    }
}