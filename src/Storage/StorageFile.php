<?php

namespace PE\Component\Cronos\Mutex\Storage;

final class StorageFile extends StorageBase
{
    /**
     * @var string
     */
    private $dirname;

    /**
     * @var callable
     */
    private $factory;

    /**
     * @param string        $dirname
     * @param callable|null $factory
     */
    public function __construct(string $dirname, callable $factory = null)
    {
        $this->dirname = $dirname;
        $this->factory = $factory ?: function (string $path): \SplFileObject {
            return new \SplFileObject($path, 'cb');
        };
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(string $name, int $wait = 0): bool
    {
        if ($file = $this->open($name)) {
            if ($wait < 1) {
                return $file->flock(LOCK_EX|LOCK_NB);
            }

            $wait *= 1000000;

            while (!$file->flock(LOCK_EX|LOCK_NB, $blocking)) {
                if ($blocking && $wait > 0) {
                    $wait -= 10000;
                    usleep(10000);
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
    public function releaseLock(string $name): bool
    {
        if ($file = $this->open($name)) {
            return $file->flock(LOCK_UN);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function containLock(string $name): bool
    {
        if ($this->acquireLock($name, 0)) {
            return !$this->releaseLock($name);
        }

        return true;
    }

    /**
     * @param string $name
     *
     * @return \SplFileObject|null
     */
    private function open(string $name): ?\SplFileObject
    {
        try {
            return call_user_func($this->factory, "{$this->dirname}/{$name}.lock");
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
