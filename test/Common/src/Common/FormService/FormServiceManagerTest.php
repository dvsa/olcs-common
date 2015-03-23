<?php

/**
 * Form Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\FormService\FormServiceManager;

/**
 * Form Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormServiceManagerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new FormServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testConstructWithConfig()
    {
        $config = m::mock('\Zend\ServiceManager\ConfigInterface');

        $config->shouldReceive('configureServiceManager')
            ->with(m::type('\Common\FormService\FormServiceManager'));

        new FormServiceManager($config);
    }

    public function testInitializeWithoutInterface()
    {
        $instance = m::mock();
        $instance->shouldReceive('setServiceLocator')
            ->never()
            ->shouldReceive('setFormServiceLocator')
            ->with($this->sut);

        $this->sut->initialize($instance);
    }

    public function testInitializeWithInterface()
    {
        $instance = m::mock('\Zend\ServiceManager\ServiceLocatorAwareInterface');
        $instance->shouldReceive('setServiceLocator')
            ->once()
            ->with($this->sm)
            ->shouldReceive('setFormServiceLocator')
            ->with($this->sut);

        $this->sut->initialize($instance);
    }

    public function testInitializeWithFormHelperInterface()
    {
        $helper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->sm->setService('Helper\Form', $helper);

        $instance = m::mock('\Common\FormService\FormHelperAwareInterface');
        $instance->shouldReceive('setFormHelper')
            ->once()
            ->with($helper)
            ->shouldReceive('setFormServiceLocator')
            ->with($this->sut);

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
        $plugin = m::mock('\Common\FormService\FormServiceInterface');

        $this->assertNull($this->sut->validatePlugin($plugin));
    }
}
