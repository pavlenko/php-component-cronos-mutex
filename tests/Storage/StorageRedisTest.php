<?php

namespace PE\Component\Cronos\Mutex\Tests\Storage;

use PE\Component\Cronos\Mutex\Storage\StorageRedis;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class StorageRedisTest extends TestCase
{
    private const NAME = 'MUTEX';

    /**
     * @var Client|MockObject
     */
    private $client;

    /**
     * @var StorageRedis
     */
    private $storage;

    protected function setUp(): void
    {
        $this->client  = $this->createPartialMock(Client::class, ['setnx', 'del', 'exists']);
        $this->storage = new StorageRedis($this->client);
    }

    public function testConstructWithInvalidClient(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new StorageRedis(new \stdClass());
    }

    public function testAcquireLock(): void
    {
        $this->client->expects(self::once())->method('setnx')->with(self::NAME, '1')->willReturn(true);

        self::assertTrue($this->storage->acquireLock(self::NAME));
    }

    public function testReleaseLock(): void
    {
        $this->client->expects(self::once())->method('del')->with(self::NAME)->willReturn(true);

        self::assertTrue($this->storage->releaseLock(self::NAME));
    }

    public function testContainLock(): void
    {
        $this->client->expects(self::once())->method('exists')->with(self::NAME)->willReturn(true);

        self::assertTrue($this->storage->containLock(self::NAME));
    }
}
