<?php

/**
 * Form Service Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService;

use CommonTest\Bootstrap;
use Common\FormService\FormServiceInterface;
use Common\FormService\FormServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;

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
        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')
            ->with(m::type(FormServiceManager::class));

        new FormServiceManager($config);
    }

    public function testValidate()
    {
        $plugin = m::mock(FormServiceInterface::class);

        $this->assertNull($this->sut->validate($plugin));
    }

    public function testValidateInvalid()
    {
        $this->expectException(InvalidServiceException::class);

        $this->sut->validate(null);
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePluginInvalid()
    {
        $this->expectException(RuntimeException::class);

        $this->sut->validatePlugin(null);
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePlugin()
    {
        $plugin = m::mock(FormServiceInterface::class);

        $this->assertNull($this->sut->validatePlugin($plugin));
    }
}
