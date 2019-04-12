<?php

namespace PE\Component\Cronos\Mutex;

use PE\Component\Cronos\Mutex\Storage\StorageInterface;

final class Mutex implements MutexInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param string           $name
     * @param StorageInterface $storage
     */
    public function __construct(string $name, StorageInterface $storage)
    {
        $this->name    = $name;
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(): bool
    {
        return $this->storage->acquireLock($this->name);
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(): bool
    {
        return $this->storage->releaseLock($this->name);
    }

    /**
     * @inheritDoc
     */
    public function containLock(): bool
    {
        return $this->storage->containLock($this->name);
    }
}
