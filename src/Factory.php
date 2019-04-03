<?php

namespace PE\Component\Cronos\Mutex;

use PE\Component\Cronos\Mutex\Storage\StorageInterface;

final class Factory implements FactoryInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function create(string $name): MutexInterface
    {
        return new Mutex($name, $this->storage);
    }
}
