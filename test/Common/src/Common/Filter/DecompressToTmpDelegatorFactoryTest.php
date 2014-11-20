<?php

namespace CommonTest\Filter;

use Common\Filter\DecompressToTmpDelegatorFactory;
use Mockery as m;
use Common\Filter\DecompressUploadToTmp;

/**
 * Class DecompressToTmpDelegatorFactoryTest
 * @package CommonTest\Filter
 */
class DecompressToTmpDelegatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDelegatorWithName()
    {
        $callback = function () {
            return new DecompressUploadToTmp();
        };

        $mockFileSystem = m::mock('Common\Filesystem\Filesystem');

        $mockSl = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockSl->shouldReceive('getServiceLocator->get')->with('Config')->andReturn(['tmpDirectory' => '/tmp/']);
        $mockSl->shouldReceive('getServiceLocator->get')
               ->with('Common\Filesystem\Filesystem')
               ->andReturn($mockFileSystem);

        $sut = new DecompressToTmpDelegatorFactory();

        /** @var \Common\Filter\DecompressUploadToTmp $service */
        $service = $sut->createDelegatorWithName($mockSl, '', '', $callback);

        $this->assertInstanceOf('Common\Filter\DecompressUploadToTmp', $service);
        $this->assertEquals('/tmp/', $service->getTempRootDir());
        $this->assertInstanceOf('Zend\Filter\Decompress', $service->getDecompressFilter());
        $this->assertSame($mockFileSystem, $service->getFileSystem());
    }
}
