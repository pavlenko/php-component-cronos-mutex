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
     * @param int $wait
     *
     * @return bool
     */
    public function acquireLock(int $wait = 0): bool
    {
        return $this->storage->acquireLock($this->name, $wait);
    }

    /**
     * @return bool
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

    /**
     * @inheritDoc
     */
    public function synchronize(callable $callable, int $wait = 0): void
    {
        $this->storage->synchronize($this->name, $callable, $wait);
    }
}
