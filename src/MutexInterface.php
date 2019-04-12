<?php

namespace PE\Component\Cronos\Mutex;

interface MutexInterface
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
