<?php

namespace PE\Component\Cronos\Mutex\Tests;

use PE\Component\Cronos\Mutex\MutexInterface;
use PE\Component\Cronos\Mutex\Synchronize;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SynchronizeTest extends TestCase
{
    /**
     * @var MutexInterface|MockObject
     */
    private $mutex;

    /**
     * @var Synchronize
     */
    private $synchronize;

    protected function setUp(): void
    {
        $this->mutex       = $this->createMock(MutexInterface::class);
        $this->synchronize = new Synchronize($this->mutex);
    }

    /**
     * @throws \Throwable
     */
    public function testExecuteLocked(): void
    {
        $this->mutex->expects(self::once())->method('acquireLock')->willReturn(false);
        $this->mutex->expects(self::never())->method('releaseLock');

        $executed = false;
        $callable = function () use (&$executed) { $executed = true; };

        self::assertFalse($this->synchronize->execute($callable));
        self::assertFalse($executed);
    }

    /**
     * @throws \Throwable
     */
    public function testExecuteNoLock(): void
    {
        $this->mutex->expects(self::once())->method('acquireLock')->willReturn(true);
        $this->mutex->expects(self::once())->method('releaseLock');

        $executed = false;
        $callable = function () use (&$executed) { $executed = true; };

        self::assertTrue($this->synchronize->execute($callable));
        self::assertTrue($executed);
    }

    /**
     * @throws \Throwable
     */
    public function testExecuteException(): void
    {
        $this->mutex->expects(self::once())->method('acquireLock')->willReturn(true);
        $this->mutex->expects(self::once())->method('releaseLock');

        $this->expectException(\Exception::class);

        $this->synchronize->execute(function () { throw new \Exception(); });
    }
}
