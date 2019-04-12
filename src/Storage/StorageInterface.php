<?php

namespace PE\Component\Cronos\Mutex\Storage;

interface StorageInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function acquireLock(string $name): bool;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function releaseLock(string $name): bool;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function containLock(string $name): bool;
}
