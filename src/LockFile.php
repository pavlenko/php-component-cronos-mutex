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
    public function acquireLock(): bool
    {
        if ($file = fopen($this->path, 'cb')) {
            return flock($file, LOCK_EX|LOCK_NB);
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
