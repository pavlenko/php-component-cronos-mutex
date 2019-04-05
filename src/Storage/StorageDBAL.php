<?php

namespace PE\Component\Cronos\Mutex\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

final class StorageDBAL implements StorageInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(string $name, int $wait = 0): bool
    {
        return !$this->containLock($name) && (bool) $this->connection->fetchColumn(sprintf(
            'SELECT GET_LOCK(%s, %s)',
            $this->connection->quote($name, Type::STRING),
            $this->connection->quote($wait, Type::INTEGER)
        ));
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(string $name): bool
    {
        return (bool) $this->connection->fetchColumn(sprintf(
            'SELECT RELEASE_LOCK(%s)',
            $this->connection->quote($name, Type::STRING)
        ));
    }

    /**
     * @inheritDoc
     */
    public function containLock(string $name): bool
    {
        return (bool) $this->connection->fetchColumn(sprintf(
            'SELECT IS_FREE_LOCK(%s)',
            $this->connection->quote($name, Type::STRING)
        ));
    }
}
