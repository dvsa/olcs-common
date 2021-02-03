<?php

namespace CommonTest\FormService;

use Common\FormService\FormHelperAwareInterface;
use Common\FormService\FormServiceManagerInitializer;
use Common\Service\Helper\FormHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use stdClass;

/**
 * Class FormServiceManagerInitializerTest
 * @package CommonTest\FormService
 */
class FormServiceManagerInitializerTest extends MockeryTestCase
{
    private $serviceLocator;

    private $sut;

    public function setUp(): void
    {
        $this->serviceLocator = m::mock(ServiceLocatorInterface::class);

        $this->sut = new FormServiceManagerInitializer();
    }

    public function testInitializeWhenInstanceSupportsNeitherInterface()
    {
        $instance = m::mock(stdClass::class);
        $instance->shouldReceive('setFormServiceLocator')
            ->with($this->serviceLocator)
            ->once();

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $this->serviceLocator)
        );
    }

    public function testInvokeWhenInstanceSupportsNeitherInterface()
    {
        $instance = m::mock(stdClass::class);
        $instance->shouldReceive('setFormServiceLocator')
            ->with($this->serviceLocator)
            ->once();

        $this->assertSame(
            $instance,
            ($this->sut)($this->serviceLocator, $instance)
        );
    }

    public function testInitializeWhenInstanceServiceLocatorAwareOnly()
    {
        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);

        $this->serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(ServiceLocatorAwareInterface::class);
        $instance->shouldReceive('setFormServiceLocator')
            ->with($this->serviceLocator)
            ->once();
        $instance->shouldReceive('setServiceLocator')
            ->with($parentServiceLocator)
            ->once();

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $this->serviceLocator)
        );
    }

    public function testInvokeWhenInstanceServiceLocatorAwareOnly()
    {
        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);

        $this->serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(ServiceLocatorAwareInterface::class);
        $instance->shouldReceive('setFormServiceLocator')
            ->with($this->serviceLocator)
            ->once();
        $instance->shouldReceive('setServiceLocator')
            ->with($parentServiceLocator)
            ->once();

        $this->assertSame(
            $instance,
            ($this->sut)($this->serviceLocator, $instance)
        );
    }

    public function testInitializeWhenInstanceFormHelperAwareOnly()
    {
        $formHelper = m::mock(FormHelperService::class);

        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);
        $parentServiceLocator->shouldReceive('get')
            ->with('Helper\Form')
            ->andReturn($formHelper);

        $this->serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(FormHelperAwareInterface::class);
        $instance->shouldReceive('setFormServiceLocator')
            ->with($this->serviceLocator)
            ->once();
        $instance->shouldReceive('setFormHelper')
            ->with($formHelper)
            ->once();

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $this->serviceLocator)
        );
    }

    public function testInvokeWhenInstanceFormHelperAwareOnly()
    {
        $formHelper = m::mock(FormHelperService::class);

        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);
        $parentServiceLocator->shouldReceive('get')
            ->with('Helper\Form')
            ->andReturn($formHelper);

        $this->serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(FormHelperAwareInterface::class);
        $instance->shouldReceive('setFormServiceLocator')
            ->with($this->serviceLocator)
            ->once();
        $instance->shouldReceive('setFormHelper')
            ->with($formHelper)
            ->once();

        $this->assertSame(
            $instance,
            ($this->sut)($this->serviceLocator, $instance)
        );
    }

    public function testInitializeWhenInstanceServiceLocatorAndFormHelperAware()
    {
        $formHelper = m::mock(FormHelperService::class);

        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);
        $parentServiceLocator->shouldReceive('get')
            ->with('Helper\Form')
            ->andReturn($formHelper);

        $this->serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(ServiceLocatorAwareInterface::class, FormHelperAwareInterface::class);
        $instance->shouldReceive('setFormServiceLocator')
            ->with($this->serviceLocator)
            ->once();
        $instance->shouldReceive('setServiceLocator')
            ->with($parentServiceLocator)
            ->once();
        $instance->shouldReceive('setFormHelper')
            ->with($formHelper)
            ->once();

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $this->serviceLocator)
        );
    }

    public function testInvokeWhenInstanceServiceLocatorAndFormHelperAware()
    {
        $formHelper = m::mock(FormHelperService::class);

        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);
        $parentServiceLocator->shouldReceive('get')
            ->with('Helper\Form')
            ->andReturn($formHelper);

        $this->serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(ServiceLocatorAwareInterface::class, FormHelperAwareInterface::class);
        $instance->shouldReceive('setFormServiceLocator')
            ->with($this->serviceLocator)
            ->once();
        $instance->shouldReceive('setServiceLocator')
            ->with($parentServiceLocator)
            ->once();
        $instance->shouldReceive('setFormHelper')
            ->with($formHelper)
            ->once();

        $this->assertSame(
            $instance,
            ($this->sut)($this->serviceLocator, $instance)
        );
    }
}
