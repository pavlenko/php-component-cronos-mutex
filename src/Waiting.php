<?php

namespace PE\Component\Cronos\Mutex;

final class Waiting implements MutexInterface
{
    /**
     * @var MutexInterface
     */
    private $mutex;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var int
     */
    private $interval;

    /**
     * @param MutexInterface $mutex
     * @param int            $timeout  Wait timeout in milliseconds
     * @param int            $interval Check interval in milliseconds
     */
    public function __construct(MutexInterface $mutex, int $timeout = 500, int $interval = 100)
    {
        $this->mutex    = $mutex;
        $this->timeout  = max(500, $timeout) * 1000;
        $this->interval = max(100, $interval) * 1000;
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(): bool
    {
        // Prevent modify configured timeout
        $timeout = $this->timeout;

        while (!$this->mutex->acquireLock()) {
            $timeout -= $this->interval;

            if ($timeout <= 0) {
                return false;
            }

            usleep($this->interval);
        }

        return true;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore Bypass
     */
    public function releaseLock(): bool
    {
        return $this->mutex->releaseLock();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore Bypass
     */
    public function containLock(): bool
    {
        return $this->mutex->containLock();
    }
}
