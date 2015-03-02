<?php

/**
 * Crud Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Crud;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Crud\CrudServiceManager;
use CommonTest\Bootstrap;

/**
 * Crud Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudServiceManagerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new CrudServiceManager();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testConstructWithConfig()
    {
        $config = m::mock('\Zend\ServiceManager\ConfigInterface');

        $config->shouldReceive('configureServiceManager');

        new CrudServiceManager($config);
    }

    public function testInitialize()
    {
        $instance = m::mock();

        $this->assertNull($this->sut->initialize($instance));
    }

    public function testInitializeWithServiceLocatorAware()
    {
        $instance = m::mock('\Zend\ServiceManager\ServiceLocatorAwareInterface');

        $instance->shouldReceive('setServiceLocator')
            ->with($this->sm);

        $this->assertNull($this->sut->initialize($instance));
    }

    public function testValidatePlugin()
    {
        $this->setExpectedException('\Zend\ServiceManager\Exception\RuntimeException');

        $plugin = m::mock();

        $this->sut->validatePlugin($plugin);
    }

    public function testValidatePluginWithValid()
    {
        $plugin = m::mock('\Common\Service\Crud\CrudServiceInterface');

        $this->assertNull($this->sut->validatePlugin($plugin));
    }
}
