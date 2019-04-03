<?php

namespace PE\Component\Cronos\Mutex\Tests\Storage;

use PE\Component\Cronos\Mutex\Storage\StorageBase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StorageBaseTest extends TestCase
{
    /**
     * @var StorageBase|MockObject
     */
    private $storage;

    protected function setUp(): void
    {
        $this->storage = $this->getMockForAbstractClass(StorageBase::class);
    }

    public function testSynchronizeNoAcquired(): void
    {
        $this->storage
            ->expects(self::once())
            ->method('acquireLock')
            ->with('NAME', 1000)
            ->willReturn(false);

        $this->storage
            ->expects(self::never())
            ->method('releaseLock');

        $this->storage->synchronize('NAME', function () {}, 1000);
    }

    public function testSynchronizeNoException(): void
    {
        $this->storage
            ->expects(self::once())
            ->method('acquireLock')
            ->with('NAME', 1000)
            ->willReturn(true);

        $this->storage
            ->expects(self::once())
            ->method('releaseLock')
            ->with('NAME');

        $this->storage->synchronize('NAME', function () {}, 1000);
    }

    public function testSynchronizeCallableThrowsException(): void
    {
        $callable = function () { throw new \Exception(); };

        $this->storage
            ->expects(self::once())
            ->method('acquireLock')
            ->with('NAME', 1000)
            ->willReturn(true);

        $this->storage
            ->expects(self::once())
            ->method('releaseLock')
            ->with('NAME');

        $this->storage->synchronize('NAME', $callable, 1000);
    }
}
