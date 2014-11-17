<?php

namespace CommonTest\Filter;

use Common\Filter\DecompressUploadToTmpFactory;
use Mockery as m;

/**
 * Class DecompressUploadToTmpFactoryTest
 * @package CommonTest\Filter
 */
class DecompressUploadToTmpFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $mockSl = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockSl->shouldReceive('getServiceLocator->get')->with('Config')->andReturn(['tmpDirectory' => '/tmp/']);

        $sut = new DecompressUploadToTmpFactory();

        /** @var \Common\Filter\DecompressUploadToTmp $service */
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Common\Filter\DecompressUploadToTmp', $service);
        $this->assertEquals('/tmp/', $service->getTempRootDir());
        $this->assertInstanceOf('Zend\Filter\Decompress', $service->getDecompressFilter());
        $this->assertInstanceOf('Common\Filesystem\Filesystem', $service->getFileSystem());
    }
}
