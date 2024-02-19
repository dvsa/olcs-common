<?php

namespace CommonTest\Filesystem;

use Common\Filesystem\Filesystem;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\FlockStore;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class FilesystemTest
 * @package CommonTest\Filesystem
 */
class FilesystemTest extends TestCase
{
    public function testCreateTmpDir()
    {
        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpDir(vfsStream::url('tmp/'));

        $this->assertTrue(is_dir($dir));
    }

    public function testCreateTmpFile()
    {
        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpFile(vfsStream::url('tmp/'));

        $this->assertTrue(file_exists($dir));
    }

    public function testCreateTmpFileWithLock()
    {
        vfsStream::setup('tmp');

        $sut = new class extends Filesystem {
            protected function getLock(string $path): LockInterface
            {
                $mock =  m::mock(FlockStore::class, LockInterface::class)
                    ->shouldReceive('acquire')
                    ->andThrow(LockConflictedException::class)
                    ->times(3)
                    ->getMock();
                return $mock;
            }
        };

        $this->expectException(LockConflictedException::class);

        $dir = $sut->createTmpFile(vfsStream::url('tmp/'), '');

        $this->assertFalse(file_exists($dir));
    }
}
