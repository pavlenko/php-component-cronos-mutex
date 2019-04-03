<?php

namespace PE\Component\Cronos\Mutex\Tests\Storage;

use PE\Component\Cronos\Mutex\Storage\StorageFile;
use PHPUnit\Framework\TestCase;

//TODO maybe create our own filesystem abstraction for allow mocking
class StorageFileTest extends TestCase
{
    public function testConstruct(): void
    {
        new StorageFile('DIR');
        $this->markTestIncomplete('there are nothing more to test');
    }

    public function testConstructFileException(): void
    {
        //TODO
        $storage = new StorageFile('DIR', function ($path) {
            $mock = $this->createMock(\SplTempFileObject::class);
            $mock
                ->expects(self::once())
                ->method('__construct')
                ->with('DIR/MUTEX.lock')
                ->willThrowException(new \Exception());

            $mock->expects(self::never())->method('flock');

            return $mock;
        });

        self::assertFalse($storage->releaseLock('MUTEX'));
    }

    public function testAcquireLock()
    {
        $this->markTestSkipped();
    }

    public function testReleaseLock()
    {
        $storage = new StorageFile('DIR', function ($path) {
            $mock = $this->createMock(\SplTempFileObject::class);
            $mock
                ->expects(self::once())
                ->method('__construct')
                ->with('DIR/MUTEX.lock');

            $mock->expects(self::never())->method('flock')->willReturn(true);

            return $mock;
        });

        self::assertTrue($storage->releaseLock('MUTEX'));
    }

    public function testContainLock()
    {
        $this->markTestSkipped();
    }
}
