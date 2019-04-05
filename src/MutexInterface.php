<?php

namespace PE\Component\Cronos\Mutex;

interface MutexInterface
{
    /**
     * @param callable $callable
     * @param int      $wait
     */
    public function synchronize(callable $callable, int $wait = 0): void;
}
