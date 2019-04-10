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
     * @var string
     */
    private $tableName;

    /**
     * @param Connection $connection
     * @param string     $tableName
     */
    public function __construct(Connection $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->tableName  = $tableName;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     *
     * @codeCoverageIgnore
     */
    public function initialize()
    {
        $platform  = $this->connection->getDatabasePlatform();
        $schemaOld = $this->connection->getSchemaManager()->createSchema();
        $schemaNew = clone $schemaOld;

        if ($schemaOld->hasTable($this->tableName)) {
            $schemaNew->dropTable($this->tableName);
        }

        $table = $schemaNew->createTable($this->tableName);
        $table->addColumn('id', Type::INTEGER, ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('name', Type::STRING, ['length' => 255, 'notnull' => true]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name'], 'idx_name');

        foreach ($schemaOld->getMigrateToSql($schemaNew, $platform) as $sql) {
            $this->connection->executeQuery($sql);
        }
    }

    /**
     * @inheritDoc
     */
    public function acquireLock(string $name, int $wait = 0): bool
    {
        return (bool) $this->connection->insert($this->tableName, ['name' => $name]);
    }

    /**
     * @inheritDoc
     */
    public function releaseLock(string $name): bool
    {
        return (bool) $this->connection->delete($this->tableName, ['name' => $name]);
    }

    /**
     * @inheritDoc
     */
    public function containLock(string $name): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('id')
            ->from($this->tableName)
            ->where($query->expr()->eq('name', $this->connection->quote($name, Type::STRING)));

        return (bool) $this->connection->fetchColumn($query->getSQL());
    }
}
