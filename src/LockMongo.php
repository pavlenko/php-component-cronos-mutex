<?php

namespace PE\Component\Cronos\Mutex;

use MongoDB\Collection;

class LockMongo implements LockInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var string
     */
    private $name;

    /**
     * @param Collection $collection
     * @param string     $name
     */
    public function __construct(Collection $collection, string $name)
    {
        $this->collection = $collection;
        $this->name       = $name;
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(): bool
    {
        return $this->collection->insertOne(['name' => $this->name])->getInsertedCount() > 0;
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(): bool
    {
        return $this->collection->deleteOne(['name' => $this->name])->getDeletedCount() > 0;
    }

    /**
     * @inheritDoc
     */
    public function containLock(): bool
    {
        return null !== $this->collection->findOne(['name' => $this->name]);
    }
}
