<?php

namespace CommonTest\Common\Service\Data;

use Common\Service\Api\Resolver;
use Common\Service\Data\RestClientAwareInitializer;
use Common\Service\Data\Interfaces\RestClientAware;
use Common\Util\RestClient;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;
use stdClass;

/**
 * Class RestClientAwareInitializerTest
 * @package CommonTest\Service\Data
 */
class RestClientAwareInitializerTest extends MockeryTestCase
{
    private $sut;

    public function setUp(): void
    {
        $this->sut = new RestClientAwareInitializer();
    }

    public function testInitializeWhenInstanceNotRestClientAware()
    {
        $instance = m::mock(stdClass::class);
        $serviceLocator = m::mock(ServiceLocatorInterface::class);

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $serviceLocator)
        );
    }

    public function testInitializeWhenInstanceRestClientAware()
    {
        $lang = 'en_GB';
        $serviceName = 'ServiceName';

        $restClient = m::mock(RestClient::class);
        $restClient->shouldReceive('setLanguage')
            ->with($lang)
            ->once();

        $resolver = m::mock(Resolver::class);
        $resolver->shouldReceive('getClient')
            ->with($serviceName)
            ->andReturn($restClient);

        $translator = m::mock(Translator::class);
        $translator->shouldReceive('getLocale')
            ->withNoArgs()
            ->andReturn($lang);

        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);
        $parentServiceLocator->shouldReceive('get')
            ->with('ServiceApiResolver')
            ->andReturn($resolver);
        $parentServiceLocator->shouldReceive('get')
            ->with('translator')
            ->andReturn($translator);

        $serviceLocator = m::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(RestClientAware::class);
        $instance->shouldReceive('setRestClient')
            ->with($restClient)
            ->once();
        $instance->shouldReceive('getServiceName')
            ->withNoArgs()
            ->andReturn($serviceName);

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $serviceLocator)
        );
    }

    public function testInvokeWhenInstanceNotRestClientAware()
    {
        $instance = m::mock(stdClass::class);
        $serviceLocator = m::mock(ServiceLocatorInterface::class);

        $this->assertSame(
            $instance,
            ($this->sut)($serviceLocator, $instance)
        );
    }

    public function testInvokeWhenInstanceRestClientAware()
    {
        $lang = 'en_GB';
        $serviceName = 'ServiceName';

        $restClient = m::mock(RestClient::class);
        $restClient->shouldReceive('setLanguage')
            ->with($lang)
            ->once();

        $resolver = m::mock(Resolver::class);
        $resolver->shouldReceive('getClient')
            ->with($serviceName)
            ->andReturn($restClient);

        $translator = m::mock(Translator::class);
        $translator->shouldReceive('getLocale')
            ->withNoArgs()
            ->andReturn($lang);

        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);
        $parentServiceLocator->shouldReceive('get')
            ->with('ServiceApiResolver')
            ->andReturn($resolver);
        $parentServiceLocator->shouldReceive('get')
            ->with('translator')
            ->andReturn($translator);

        $serviceLocator = m::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(RestClientAware::class);
        $instance->shouldReceive('setRestClient')
            ->with($restClient)
            ->once();
        $instance->shouldReceive('getServiceName')
            ->withNoArgs()
            ->andReturn($serviceName);

        $this->assertSame(
            $instance,
            ($this->sut)($serviceLocator, $instance)
        );
    }
}
