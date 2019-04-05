<?php

namespace PE\Component\Cronos\Mutex\Storage;

use MongoDB\Client;
use MongoDB\Collection;

final class StorageMongoDB extends StorageBase
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Client $client
     * @param string $database
     * @param string $collection
     */
    public function __construct(Client $client, string $database, string $collection)
    {
        $this->collection = $client->selectDatabase($database)->selectCollection($collection);
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(string $name, int $wait = 0): bool
    {
        return $this->collection->insertOne(['name' => $name])->getInsertedCount() > 0;
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(string $name): bool
    {
        return $this->collection->deleteOne(['name' => $name])->getDeletedCount() > 0;
    }

    /**
     * @inheritDoc
     */
    public function containLock(string $name): bool
    {
        return $this->collection->findOne(['name' => $name]) !== null;
    }
}
