<?php

namespace PE\Component\Cronos\Mutex;

interface LockInterface
{
    /**
     * @return bool
     */
    public function acquireLock(): bool;

    /**
     * @return bool
     */
    public function releaseLock(): bool;

    /**
     * @return bool
     */
    public function containLock(): bool;
}
