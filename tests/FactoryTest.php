<?php

namespace PE\Component\Cronos\Mutex\Tests;

use PE\Component\Cronos\Mutex\Factory;
use PE\Component\Cronos\Mutex\Storage\StorageInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testCreate(): void
    {
        /* @var $storage StorageInterface|MockObject */
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects(self::once())->method('containLock')->with('NAME');

        $mutex = (new Factory($storage))->create('NAME');
        $mutex->containLock();
    }
}
