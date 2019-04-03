<?php

namespace PE\Component\Cronos\Mutex;

interface MutexInterface
{
    /**
     * @param int $wait
     *
     * @return bool
     */
    public function acquireLock(int $wait = 0): bool;

    /**
     * @return bool
     */
    public function releaseLock(): bool;

    /**
     * @return bool
     */
    public function containLock(): bool;

    /**
     * @param callable $callable
     * @param int      $wait
     */
    public function synchronize(callable $callable, int $wait = 0): void;
}
