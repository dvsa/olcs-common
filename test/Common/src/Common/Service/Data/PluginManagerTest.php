<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\PluginManager;
use Mockery as m;

/**
 * Class PluginManagerTest
 * @package CommonTest\Service\Data
 */
class PluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testValidatePlugin()
    {
        $sut = new PluginManager();
        $this->assertTrue($sut->validatePlugin('blargle'));
    }

    public function testGetChecksMainSlFirst()
    {
        $mockSL = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSL->shouldReceive('has')->with('testService')->andReturn(true);
        $mockSL->shouldReceive('get')->with('testService')->andReturn('service');

        $sut = new PluginManager();
        $sut->setServiceLocator($mockSL);
        $sut->setService('testService', 'wrongService');

        $this->assertEquals('service', $sut->get('testService'));
    }

    public function testGet()
    {
        $mockSL = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSL->shouldReceive('has')->with('testService')->andReturn(false);

        $sut = new PluginManager();
        $sut->setServiceLocator($mockSL);
        $sut->setService('testService', 'service');

        $this->assertEquals('service', $sut->get('testService'));
    }

    public function testInitializeRestClientInterface()
    {
        $mockPlugin = m::mock('stdClass');

        $sut = new PluginManager();
        $sut->initializeRestClientInterface($mockPlugin);
    }

    public function testInitializeRestClientInterfaceWithInitializableClass()
    {
        $lang = 'EN_GB';

        $mockClient = m::mock('\Common\Util\RestClient');
        $mockClient->shouldReceive('setLanguage')->with($lang);

        $mockServiceApiResolver = m::mock('stdClass');
        $mockServiceApiResolver->shouldReceive('getClient')->with('serviceName')->andReturn($mockClient);

        $mockTranslator = m::mock('stdClass');
        $mockTranslator->shouldReceive('getLocale')->andReturn($lang);

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ServiceApiResolver')->andReturn($mockServiceApiResolver);
        $mockSl->shouldReceive('get')->with('translator')->andReturn($mockTranslator);

        $mockPlugin = m::mock('\Common\Service\Data\RestClientAwareInterface');
        $mockPlugin->shouldReceive('setRestClient')->with($mockClient);
        $mockPlugin->shouldReceive('getServiceName')->andReturn('serviceName');

        $sut = new PluginManager();
        $sut->setServiceLocator($mockSl);
        $sut->initializeRestClientInterface($mockPlugin);

    }
}
