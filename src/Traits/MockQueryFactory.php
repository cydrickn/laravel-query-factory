<?php

namespace LaravelQueryFactory\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;
use Mockery;
use PDO;

trait MockQueryFactory
{
    /**
     * @param string $driver
     * @param array $methods
     * @return \Mockery\MockInterface|\Illuminate\Database\Connection
     */
    public function mockConnection(string $driver): Connection
    {
        $mockDriverFunction = 'mock' . ucwords($driver) . 'Connection';
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

    protected function mockMysqlConnection(): MySqlConnection
    {
        return Mockery::mock(MySqlConnection::class)->makePartial();
    }

    protected function mockSqliteConnection(): SQLiteConnection
    {
        return Mockery::mock(SQLiteConnection::class)->makePartial();
    }

    protected function mockPostgresConnection(): PostgresConnection
    {
        return Mockery::mock(PostgresConnection::class)->makePartial();
    }

    protected  function mockSqlserverConnection(): SqlServerConnection
    {
        return Mockery::mock(SqlServerConnection::class)->makePartial();
    }
}