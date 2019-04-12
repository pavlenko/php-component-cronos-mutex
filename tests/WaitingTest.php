<?php

namespace PE\Component\Cronos\Mutex\Tests;

use PE\Component\Cronos\Mutex\MutexInterface;
use PE\Component\Cronos\Mutex\Waiting;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WaitingTest extends TestCase
{
    /**
     * @var MutexInterface|MockObject
     */
    private $mutex;

    protected function setUp(): void
    {
        $this->mutex = $this->createMock(MutexInterface::class);
    }

    public function testAcquireLockTimeout(): void
    {
        $this->mutex->expects(self::exactly(5))->method('acquireLock');

        self::assertFalse((new Waiting($this->mutex))->acquireLock());
    }

    public function testAcquireLockDelayed(): void
    {
        $this->mutex->expects(self::exactly(3))->method('acquireLock')->willReturnOnConsecutiveCalls(false, false, true);

        self::assertTrue((new Waiting($this->mutex))->acquireLock());
    }

    public function testAcquireLockImmediate(): void
    {
        $this->mutex->expects(self::once())->method('acquireLock')->willReturn(true);

        self::assertTrue((new Waiting($this->mutex))->acquireLock());
    }
}
