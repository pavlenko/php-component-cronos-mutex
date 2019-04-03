<?php

namespace PE\Component\Cronos\Mutex\Storage;

interface StorageInterface
{
    /**
     * @param string $name
     * @param int    $wait
     *
     * @return bool
     */
    public function acquireLock(string $name, int $wait = 0): bool;

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
     * @param string   $name
     * @param callable $callable
     * @param int      $wait
     */
    public function synchronize(string $name, callable $callable, int $wait = 0): void;
}
