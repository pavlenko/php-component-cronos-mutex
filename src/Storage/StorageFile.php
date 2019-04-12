<?php

namespace PE\Component\Cronos\Mutex\Storage;

final class StorageFile implements StorageInterface
{
    /**
     * @var string
     */
    private $dirname;

    /**
     * @param string $dirname
     */
    public function __construct(string $dirname)
    {
        $this->dirname = $dirname;
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(string $name, int $wait = 0): bool
    {
        if ($file = @fopen("{$this->dirname}/{$name}.lock", 'cb')) {
            return flock($file, LOCK_EX|LOCK_NB);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(string $name): bool
    {
        if ($file = fopen("{$this->dirname}/{$name}.lock", 'cb')) {
            flock($file, LOCK_UN|LOCK_NB);
            fclose($file);

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function containLock(string $name): bool
    {
        if ($this->acquireLock($name)) {
            return !$this->releaseLock($name);
        }

        return true;
    }
}
