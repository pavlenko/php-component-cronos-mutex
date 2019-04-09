<?php

namespace PE\Component\Cronos\Mutex;

interface MutexInterface
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

    /**
     * @param callable $callable
     */
    public function synchronize(callable $callable): void;
}
