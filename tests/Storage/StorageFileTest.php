<?php

namespace PE\Component\Cronos\Mutex\Tests\Storage;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;
use PE\Component\Cronos\Mutex\Storage\StorageFile;
use PHPUnit\Framework\TestCase;

class StorageFileTest extends TestCase
{
    public function testAcquireNoFile(): void
    {
        $file = new vfsStreamFile('FOO.lock', vfsStreamWrapper::READONLY);

        $root = vfsStream::setup();
        $root->addChild($file);

        self::assertFalse((new StorageFile($root->url()))->acquireLock('FOO'));
    }

    public function testAcquireNoLock(): void
    {
        $file = new vfsStreamFile('FOO.lock');

        $root = vfsStream::setup();
        $root->addChild($file);

        $file->lock(fopen($file->url(), 'wb'), LOCK_SH);

        self::assertFalse((new StorageFile($root->url()))->acquireLock('FOO'));
    }
}
