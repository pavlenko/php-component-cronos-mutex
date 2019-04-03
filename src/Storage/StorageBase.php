<?php

namespace PE\Component\Cronos\Mutex\Storage;

abstract class StorageBase implements StorageInterface
{
    /**
     * @inheritDoc
     */
    final public function synchronize(string $name, callable $callable, int $wait = 0): void
    {
        if ($this->acquireLock($name, $wait)) {
            try {
                $callable();
            } catch (\Throwable $exception) {}

            $this->releaseLock($name);
        }
    }
}
