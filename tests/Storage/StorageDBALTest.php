<?php

namespace PE\Component\Cronos\Mutex\Tests\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use PE\Component\Cronos\Mutex\Storage\StorageDBAL;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StorageDBALTest extends TestCase
{
    private const NAME = 'MUTEX';

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

        $this->storage = new StorageDBAL($this->connection);
    }

    public function testAcquireLockAlreadyContain(): void
    {
        $this->connection
            ->expects(self::any())
            ->method('fetchColumn')
            ->withConsecutive(
                [sprintf('SELECT IS_FREE_LOCK(\'%s\')', self::NAME)]
            )
            ->willReturnOnConsecutiveCalls(
                '1'
            );

        self::assertFalse($this->storage->acquireLock(self::NAME));
    }

    public function testAcquireLockReturnsTrue(): void
    {
        $this->connection
            ->expects(self::any())
            ->method('fetchColumn')
            ->withConsecutive(
                [sprintf('SELECT IS_FREE_LOCK(\'%s\')', self::NAME)],
                [sprintf('SELECT GET_LOCK(\'%s\', %s)', self::NAME, 1000)]
            )
            ->willReturnOnConsecutiveCalls(
                '0',
                '1'
            );

        self::assertTrue($this->storage->acquireLock(self::NAME, 1000));
    }

    public function testAcquireLockReturnsFalse(): void
    {
        $this->connection
            ->expects(self::any())
            ->method('fetchColumn')
            ->withConsecutive(
                [sprintf('SELECT IS_FREE_LOCK(\'%s\')', self::NAME)],
                [sprintf('SELECT GET_LOCK(\'%s\', %s)', self::NAME, 1000)]
            )
            ->willReturnOnConsecutiveCalls(
                '0',
                '0'
            );

        self::assertFalse($this->storage->acquireLock(self::NAME, 1000));
    }

    public function testReleaseLockReturnsTrue(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('fetchColumn')
            ->with(sprintf('SELECT RELEASE_LOCK(\'%s\')', self::NAME))
            ->willReturn('1');

        self::assertTrue($this->storage->releaseLock(self::NAME));
    }

    public function testReleaseLockReturnsFalse(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('fetchColumn')
            ->with(sprintf('SELECT RELEASE_LOCK(\'%s\')', self::NAME))
            ->willReturn('0');

        self::assertFalse($this->storage->releaseLock(self::NAME));
    }

    public function testContainLockReturnsTrue(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('fetchColumn')
            ->with(sprintf('SELECT IS_FREE_LOCK(\'%s\')', self::NAME))
            ->willReturn('1');

        self::assertTrue($this->storage->containLock(self::NAME));
    }

    public function testContainLockReturnsFalse(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('fetchColumn')
            ->with(sprintf('SELECT IS_FREE_LOCK(\'%s\')', self::NAME))
            ->willReturn('0');

        self::assertFalse($this->storage->containLock(self::NAME));
    }
}
