<?php

namespace PE\Component\Cronos\Mutex\Tests\Storage;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\DeleteResult;
use MongoDB\InsertOneResult;
use PE\Component\Cronos\Mutex\Storage\StorageMongoDB;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StorageMongoDBTest extends TestCase
{
    private const NAME       = 'MUTEX';
    private const DATABASE   = 'database';
    private const COLLECTION = 'collection';

    /**
     * @var Collection|MockObject
     */
    private $collection;

    /**
     * @var StorageMongoDB
     */
    private $storage;

    protected function setUp(): void
    {
        $this->collection = $this->createMock(Collection::class);

        $database = $this->createMock(Database::class);
        $database
            ->expects(self::once())
            ->method('selectCollection')
            ->with(self::COLLECTION)
            ->willReturn($this->collection);

        $client = $this->createMock(Client::class);
        $client
            ->expects(self::once())
            ->method('selectDatabase')
            ->with(self::DATABASE)
            ->willReturn($database);

        $this->storage = new StorageMongoDB($client, self::DATABASE, self::COLLECTION);
    }

    public function testAcquireLock(): void
    {
        $result = $this->createMock(InsertOneResult::class);
        $result->expects(self::once())->method('getInsertedCount')->willReturn(1);

        $this->collection
            ->expects(self::once())
            ->method('insertOne')
            ->with(['name' => self::NAME])
            ->willReturn($result);

        self::assertTrue($this->storage->acquireLock(self::NAME));
    }

    public function testReleaseLock(): void
    {
        $result = $this->createMock(DeleteResult::class);
        $result->expects(self::once())->method('getDeletedCount')->willReturn(1);

        $this->collection
            ->expects(self::once())
            ->method('deleteOne')
            ->with(['name' => self::NAME])
            ->willReturn($result);

        self::assertTrue($this->storage->releaseLock(self::NAME));
    }

    public function testContainLock(): void
    {
        $this->collection
            ->expects(self::once())
            ->method('findOne')
            ->with(['name' => self::NAME])
            ->willReturn(null);

        self::assertFalse($this->storage->containLock(self::NAME));
    }
}
