<?php

namespace CommonTest\Common\Service\Data;

use Common\Service\Data\PluginManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class PluginManagerTest
 * @package CommonTest\Service\Data
 */
class PluginManagerTest extends MockeryTestCase
{
    public function testValidate()
    {
        $sut = new PluginManager();
        $this->assertNull($sut->validate(null));
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePlugin()
    {
        $sut = new PluginManager();
        $this->assertNull($sut->validatePlugin(null));
    }

    public function testGetChecksMainSlFirst()
    {
        $mockSL = m::mock('\Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSL->shouldReceive('has')->with('testService')->andReturn(true);
        $mockSL->shouldReceive('get')->with('testService')->andReturn('service');

        $sut = new PluginManager();
        $sut->setServiceLocator($mockSL);
        $sut->setService('testService', 'wrongService');

        $this->assertEquals('service', $sut->get('testService'));
    }

    public function testGet()
    {
        $mockSL = m::mock('\Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSL->shouldReceive('has')->with('testService')->andReturn(false);

        $sut = new PluginManager();
        $sut->setServiceLocator($mockSL);
        $sut->setService('testService', 'service');

        $this->assertEquals('service', $sut->get('testService'));
    }
}
