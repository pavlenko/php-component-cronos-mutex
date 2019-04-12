<?php

namespace PE\Component\Cronos\Mutex;

final class Synchronize
{
    /**
     * @var MutexInterface
     */
    private $mutex;

    /**
     * @param MutexInterface $mutex
     */
    public function __construct(MutexInterface $mutex)
    {
        $this->mutex = $mutex;
    }

    /**
     * @param callable $callable
     *
     * @return bool
     *
     * @throws \Throwable If callable throws exception
     */
    public function execute(callable $callable): bool
    {
        if (!$this->mutex->acquireLock()) {
            return false;
        }

        try {
            $callable();

            // Release lock after execution
            $this->mutex->releaseLock();

            // Return success execution
            return true;
        } catch (\Throwable $exception) {
            // Release lock if error occurred
            $this->mutex->releaseLock();

            // Re-throw exception after release lock
            throw $exception;
        }
    }
}
