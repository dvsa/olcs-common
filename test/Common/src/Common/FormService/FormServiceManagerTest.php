<?php

/**
 * Form Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService;

use CommonTest\Bootstrap;
use Common\FormService\FormServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Form Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormServiceManagerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new FormServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testConstructWithConfig()
    {
        $config = m::mock('\Laminas\ServiceManager\ConfigInterface');

        $config->shouldReceive('configureServiceManager')
            ->with(m::type('\Common\FormService\FormServiceManager'));

        new FormServiceManager($config);
    }

    public function testValidatePluginInvalid()
    {
        $this->expectException('\Laminas\ServiceManager\Exception\RuntimeException');

        $plugin = m::mock();

        $this->sut->validatePlugin($plugin);
    }

    public function testValidatePlugin()
    {
        $plugin = m::mock('\Common\FormService\FormServiceInterface');

        $this->assertNull($this->sut->validatePlugin($plugin));
    }
}
