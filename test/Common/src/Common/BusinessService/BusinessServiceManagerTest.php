<?php

/**
 * Business Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\BusinessServiceManager;

/**
 * Business Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessServiceManagerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new BusinessServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testConstructWithConfig()
    {
        $config = m::mock('\Zend\ServiceManager\ConfigInterface');

        $config->shouldReceive('configureServiceManager')
            ->with(m::type('\Common\BusinessService\BusinessServiceManager'));

        new BusinessServiceManager($config);
    }

    public function testInitializeWithoutInterface()
    {
        $instance = m::mock();
        $instance->shouldReceive('setServiceLocator')
            ->never();

        $this->sut->initialize($instance);
    }

    public function testInitializeWithInterface()
    {
        $instance = m::mock('\Zend\ServiceManager\ServiceLocatorAwareInterface');
        $instance->shouldReceive('setServiceLocator')
            ->once()
            ->with($this->sm);

        $this->sut->initialize($instance);
    }

    public function testInitializeWithBusinessServiceAwareInterface()
    {
        $instance = m::mock('\Common\BusinessService\BusinessServiceAwareInterface');
        $instance->shouldReceive('setBusinessServiceManager')
            ->once()
            ->with($this->sut);

        $this->sut->initialize($instance);
    }

    public function testInitializeWithBusinessRuleAwareInterface()
    {
        $brm = m::mock('\Common\BusinessRule\BusinessRuleManager');
        $this->sm->setService('BusinessRuleManager', $brm);

        $instance = m::mock('\Common\BusinessRule\BusinessRuleAwareInterface');
        $instance->shouldReceive('setBusinessRuleManager')
            ->once()
            ->with($brm);

        $this->sut->initialize($instance);
    }

    public function testValidatePluginInvalid()
    {
        $this->setExpectedException('\Zend\ServiceManager\Exception\RuntimeException');

        $plugin = m::mock();

        $this->sut->validatePlugin($plugin);
    }

    public function testValidatePlugin()
    {
        $plugin = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->assertNull($this->sut->validatePlugin($plugin));
    }
}
