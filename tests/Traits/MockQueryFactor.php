<?php

namespace LaravelQueryFactory\Tests\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Mockery;
use Mockery\MockInterface;
use PDO;

trait MockQueryFactor
{
    /**
     * @param string $driver
     * @param array $methods
     * @return \Mockery\MockInterface|\Illuminate\Database\Connection
     */
    protected function mockConnection(string $driver, array $methods = []): Connection
    {
        $mockDriverFunction = 'mock' . ucwords($driver);
        $connection = call_user_func([$this, $mockDriverFunction], $methods);
        $connection->setPdo($this->mockPdo());

        return $connection;
    }

    /**
     * @param array $methods
     * @return \Illuminate\Database\MySqlConnection|MockInterface
     */
    protected function mockMysql(array $methods = []): MySqlConnection
    {
        return Mockery::mock(MySqlConnection::class)->makePartial();
    }

    protected function mockPdo(): PDO
    {
        $pdo = Mockery::mock(PDO::class);
        $pdo->shouldReceive('prepare')->andReturn(new \PDOStatement($pdo));

        return $pdo;
    }
}