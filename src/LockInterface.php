<?php

namespace PE\Component\Cronos\Mutex;

interface LockInterface
{
    /**
     * @param int $waitMS
     *
     * @return bool
     */
    public function acquireLock(int $waitMS = 0): bool;

    /**
     * @return bool
     */
    public function releaseLock(): bool;

    /**
     * @return bool
     */
    public function containLock(): bool;
}
