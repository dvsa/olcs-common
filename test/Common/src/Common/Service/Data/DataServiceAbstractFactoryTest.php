<?php

namespace CommonTest\Service\Data;

use Common\Data\Object\Bundle;
use Common\Service\Data\DataServiceAbstractFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class DataServiceAbstractFactoryTest
 * @package CommonTest\Service\Data
 */
class DataServiceAbstractFactoryTest extends TestCase
{
    public function testCanCreateServiceWithName()
    {
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $sut = new DataServiceAbstractFactory();
        $this->assertTrue($sut->canCreateServiceWithName($mockSl, 'name', '\Generic\Service\Data\Licence'));
        $this->assertFalse($sut->canCreateServiceWithName($mockSl, 'name', '\Common\Service\Data\Licence'));
        $this->assertFalse($sut->canCreateServiceWithName($mockSl, 'name', 'SomeOtherService'));
    }

    public function testCreateServiceWithName()
    {
        $bundle = new Bundle();

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('BundleManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->once()->with('Licence')->andReturn($bundle);

        $sut = new DataServiceAbstractFactory();

        $service = $sut->createServiceWithName($mockSl, 'name', '\Generic\Service\Data\Licence');
        $this->assertInstanceOf('Common\Service\Data\Generic', $service);
        $this->assertEquals('Licence', $service->getServiceName());
    }
}
