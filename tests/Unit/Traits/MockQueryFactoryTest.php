<?php

namespace LaravelQueryFactory\Tests\Unit\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use LaravelQueryFactory\Tests\Unit\TestCase;
use LaravelQueryFactory\Traits\MockQueryFactor;
use Mockery;
use Mockery\MockInterface;
use PDO;

class MockQueryFactoryTest extends TestCase
{
    public function testMockPdo()
    {
        $mock = $this->mockTrait();
        $pdo = $mock->mockPdo();
        $this->assertInstanceOf(MockInterface::class, $pdo);
        $this->assertInstanceOf(PDO::class, $pdo);
    }

    /**
     * @dataProvider dataMockConnection
     *
     * @param string $driver
     * @param string $instance
     */
    public function testMockConnection(string $driver, string $instance)
    {
        $mock = $this->mockTrait();
        $connection = $mock->mockConnection($driver);
        $this->assertInstanceOf(MockInterface::class, $connection);
        $this->assertInstanceOf(Connection::class, $connection);
        $this->assertInstanceOf($instance, $connection);
    }

    public function dataMockConnection()
    {
        yield ['mysql', MySqlConnection::class];
        yield ['sqlite', SQLiteConnection::class];
    }

    private function mockTrait()
    {
        return Mockery::mock(MockQueryFactor::class)->makePartial();
    }
}