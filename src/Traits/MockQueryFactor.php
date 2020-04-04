<?php

namespace LaravelQueryFactory\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
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
    public function mockConnection(string $driver): Connection
    {
        $mockDriverFunction = 'mock' . ucwords($driver);
        $connection = call_user_func([$this, $mockDriverFunction]);
        $connection->setPdo($this->mockPdo());

        return $connection;
    }

    public function mockPdo(): PDO
    {
        $pdo = Mockery::mock(PDO::class);
        $pdo->shouldReceive('prepare')->andReturn(new \PDOStatement($pdo));

        return $pdo;
    }

    protected function mockMysql(): MySqlConnection
    {
        return Mockery::mock(MySqlConnection::class)->makePartial();
    }

    protected function mockSqlite(): SQLiteConnection
    {
        return Mockery::mock(SQLiteConnection::class)->makePartial();
    }
}