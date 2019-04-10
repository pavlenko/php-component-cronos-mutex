<?php

namespace PE\Component\Cronos\Mutex\Tests\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use PE\Component\Cronos\Mutex\Storage\StorageDBAL;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StorageDBALTest extends TestCase
{
    private const NAME  = 'MUTEX';
    private const TABLE = 'table';

    /**
     * @var Connection|MockObject
     */
    private $connection;

    /**
     * @var StorageDBAL
     */
    private $storage;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);

        $this->connection->method('quote')->willReturnCallback(function ($v, $t) {
            if (Type::STRING === $t) {
                return "'$v'";
            }

            return $v;
        });

        $this->storage = new StorageDBAL($this->connection, self::TABLE);
    }

    public function testAcquireLock(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('insert')
            ->with(self::TABLE, ['name' => self::NAME])
            ->willReturn(true);

        self::assertTrue($this->storage->acquireLock(self::NAME));
    }

    public function testReleaseLock(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('delete')
            ->with(self::TABLE, ['name' => self::NAME])
            ->willReturn(true);

        self::assertTrue($this->storage->releaseLock(self::NAME));
    }

    public function testContainLock(): void
    {
        $this->connection->method('createQueryBuilder')->willReturn(new QueryBuilder($this->connection));
        $this->connection->method('getExpressionBuilder')->willReturn(new ExpressionBuilder($this->connection));

        $this->connection
            ->expects(self::exactly(2))
            ->method('fetchColumn')
            ->with(sprintf('SELECT id FROM table WHERE name = \'%s\'', self::NAME))
            ->willReturnOnConsecutiveCalls('1', null);

        self::assertTrue($this->storage->containLock(self::NAME));
        self::assertFalse($this->storage->containLock(self::NAME));
    }
}
