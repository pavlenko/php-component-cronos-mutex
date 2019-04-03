<?php

namespace PE\Component\Cronos\Mutex\Tests;

use PE\Component\Cronos\Mutex\Mutex;
use PE\Component\Cronos\Mutex\Storage\StorageInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MutexTest extends TestCase
{
    private const NAME = 'MUTEX';

    /**
     * @var StorageInterface|MockObject
     */
    private $storage;

    /**
     * @var Mutex
     */
    private $mutex;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(StorageInterface::class);
        $this->mutex   = new Mutex(self::NAME, $this->storage);
    }

    public function testAcquireLock(): void
    {
        $this->storage
            ->expects(self::once())
            ->method('acquireLock')
            ->with(self::NAME, 1000)
            ->willReturn(true);

        self::assertTrue($this->mutex->acquireLock(1000));
    }

    public function testReleaseLock(): void
    {
        $this->storage
            ->expects(self::once())
            ->method('releaseLock')
            ->with(self::NAME)
            ->willReturn(true);

        self::assertTrue($this->mutex->releaseLock());
    }

    public function testContainLock(): void
    {
        $this->storage
            ->expects(self::once())
            ->method('containLock')
            ->with(self::NAME)
            ->willReturn(true);

        self::assertTrue($this->mutex->containLock());
    }

    public function testSynchronize(): void
    {
        $callable = function () {};

        $this->storage
            ->expects(self::once())
            ->method('synchronize')
            ->with(self::NAME, $callable, 1000);

        $this->mutex->synchronize($callable, 1000);
    }
}
