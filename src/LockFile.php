<?php

namespace PE\Component\Cronos\Mutex;

class LockFile implements LockInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(int $waitMS = 0): bool
    {
        if ($file = fopen($this->path, 'cb')) {
            if ($waitMS < 1) {
                return flock($file, LOCK_EX|LOCK_NB);
            }

            while (!flock($file, LOCK_EX|LOCK_NB, $blocking)) {
                if ($blocking && $waitMS > 0) {
                    $waitMS--;
                    usleep(1000);
                } else {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(): bool
    {
        if ($file = fopen($this->path, 'cb')) {
            flock($file, LOCK_UN);
            fclose($file);
            @unlink($file);

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function containLock(): bool
    {
        if ($this->acquireLock()) {
            return !$this->releaseLock();
        }

        return true;
    }
}
