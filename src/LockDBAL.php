<?php

namespace PE\Component\Cronos\Mutex;

use Doctrine\DBAL\Connection;

class LockDBAL implements LockInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $name;

    /**
     * @inheritDoc
     */
    public function acquireLock(): bool
    {
        return !$this->containLock() && $this->connection->insert($this->table, ['name' => $this->name]);
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(): bool
    {
        return $this->connection->delete($this->table, ['name' => $this->name]);
    }

    /**
     * @inheritDoc
     */
    public function containLock(): bool
    {
        return $this->connection->fetchColumn($this->table, ['name' => $this->name]);
    }
}
