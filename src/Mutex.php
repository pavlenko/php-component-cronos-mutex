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
    public function acquireLock(string $name): bool
    {
        return $this->storage->acquireLock($name);
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(string $name): bool
    {
        return $this->storage->releaseLock($name);
    }

    /**
     * @inheritDoc
     */
    public function containLock(string $name): bool
    {
        return $this->storage->containLock($name);
    }

    /**
     * @inheritDoc
     */
    public function synchronize(callable $callable): void
    {
        if ($this->storage->acquireLock($this->name)) {
            try {
                $callable();
            } catch (\Exception $exception) {}

            $this->storage->releaseLock($this->name);
        }
    }
}
