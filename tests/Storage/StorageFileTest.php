<?php

namespace PE\Component\Cronos\Mutex\Tests\Storage;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;
use PE\Component\Cronos\Mutex\Storage\StorageFile;
use PHPUnit\Framework\TestCase;

class StorageFileTest extends TestCase
{
    protected function setUp(): void
    {
        vfsStreamWrapper::register();
    }

    protected function tearDown(): void
    {
        vfsStreamWrapper::unregister();
    }

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

        $file->lock($root, LOCK_EX);

        self::assertFalse((new StorageFile($root->url()))->acquireLock('FOO'));
    }

    public function testAcquireSuccess(): void
    {
        $root = vfsStream::setup();

        self::assertTrue((new StorageFile($root->url()))->acquireLock('FOO'));
    }

    public function testReleaseNoFile(): void
    {
        $file = new vfsStreamFile('FOO.lock', vfsStreamWrapper::READONLY);

        $root = vfsStream::setup();
        $root->addChild($file);

        self::assertFalse((new StorageFile($root->url()))->releaseLock('FOO'));
    }

    public function testReleaseSuccess(): void
    {
        $root = vfsStream::setup();

        self::assertTrue((new StorageFile($root->url()))->releaseLock('FOO'));
    }

    public function testContainLock()
    {
        $file = new vfsStreamFile('FOO.lock', vfsStreamWrapper::READONLY);

        $root = vfsStream::setup();
        $root->addChild($file);

        self::assertTrue((new StorageFile($root->url()))->containLock('FOO'));
    }

    public function testNoContainLock()
    {
        $root = vfsStream::setup();

        self::assertFalse((new StorageFile($root->url()))->containLock('FOO'));
    }
}
