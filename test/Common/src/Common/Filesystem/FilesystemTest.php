<?php

namespace CommonTest\Filesystem;

use Common\Filesystem\Filesystem;
use org\bovigo\vfs\vfsStream;

/**
 * Class FilesystemTest
 * @package CommonTest\Filesystem
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateTmpDir()
    {
        vfsStream::setup('tmp');
        $sut = new Filesystem();

        $dir = $sut->createTmpDir(vfsStream::url('tmp/'));

        $this->assertTrue(is_dir($dir));
    }
}
