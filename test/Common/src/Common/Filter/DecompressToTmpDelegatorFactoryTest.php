<?php

namespace CommonTest\Filter;

use Common\Filesystem\Filesystem;
use Common\Filter\DecompressToTmpDelegatorFactory;
use Common\Filter\DecompressUploadToTmp;
use Laminas\Filter\Decompress;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class DecompressToTmpDelegatorFactoryTest
 * @package CommonTest\Filter
 */
class DecompressToTmpDelegatorFactoryTest extends MockeryTestCase
{
    public function testInvoke()
    {
        $tmpDir = '/tmp/';
        $callback = function () {
            return new DecompressUploadToTmp();
        };

        $mockFileSystem = m::mock(Filesystem::class);

        $mockSl = m::mock(ServiceManager::class);
        $mockSl->shouldReceive('getServiceLocator->get')->with('Config')->andReturn(['tmpDirectory' => $tmpDir]);
        $mockSl->shouldReceive('getServiceLocator->get')
               ->with('Common\Filesystem\Filesystem')
               ->andReturn($mockFileSystem);

        $sut = new DecompressToTmpDelegatorFactory();

        /** @var DecompressUploadToTmp $service */
        $service = $sut($mockSl, '', $callback);

        $this->assertInstanceOf(DecompressUploadToTmp::class, $service);
        $this->assertEquals($tmpDir, $service->getTempRootDir());
        $this->assertInstanceOf(Decompress::class, $service->getDecompressFilter());
        $this->assertSame($mockFileSystem, $service->getFileSystem());
    }

    /**
     * @todo OLCS-28149
     */
    public function testCreateDelegatorWithName()
    {
        $tmpDir = '/tmp/';
        $callback = function () {
            return new DecompressUploadToTmp();
        };

        $mockFileSystem = m::mock(Filesystem::class);

        $mockSl = m::mock(ServiceManager::class);
        $mockSl->shouldReceive('getServiceLocator->get')->with('Config')->andReturn(['tmpDirectory' => $tmpDir]);
        $mockSl->shouldReceive('getServiceLocator->get')
               ->with('Common\Filesystem\Filesystem')
               ->andReturn($mockFileSystem);

        $sut = new DecompressToTmpDelegatorFactory();

        /** @var DecompressUploadToTmp $service */
        $service = $sut->createDelegatorWithName($mockSl, '', '', $callback);

        $this->assertInstanceOf(DecompressUploadToTmp::class, $service);
        $this->assertEquals($tmpDir, $service->getTempRootDir());
        $this->assertInstanceOf(Decompress::class, $service->getDecompressFilter());
        $this->assertSame($mockFileSystem, $service->getFileSystem());
    }
}
