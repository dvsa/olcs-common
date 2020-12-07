<?php

namespace CommonTest\Service\Cqrs;

use Common\Service\Cqrs\RequestFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class RequestFactoryTest
 * @package CommonTest\Service\Cqrs
 */
class RequestFactoryTest extends TestCase
{
    public function testCreateServiceWithoutSecureToken()
    {
        $cookies = [];

        $mockLogProcessor = m::mock();
        $mockLogProcessor->shouldReceive('get')->with(\Olcs\Logging\Log\Processor\RequestId::class)->once()->andReturn(
            m::mock()->shouldReceive('getIdentifier')->with()->once()->andReturn('IDENT1')->getMock()
        );

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getCookie')->andReturn($cookies);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $mockSl->shouldReceive('get')->with('LogProcessorManager')->andReturn($mockLogProcessor);
        $sut = new RequestFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(Request::class, $service);
        $this->assertEquals(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Correlation-Id' => 'IDENT1',
            ],
            $service->getHeaders()->toArray()
        );
    }

    public function testCreateServiceWithSecureToken()
    {
        $cookies = ['secureToken' => 'myToken'];

        $mockLogProcessor = m::mock();
        $mockLogProcessor->shouldReceive('get')->with(\Olcs\Logging\Log\Processor\RequestId::class)->once()->andReturn(
            m::mock()->shouldReceive('getIdentifier')->with()->once()->andReturn('IDENT1')->getMock()
        );

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getCookie')->andReturn($cookies);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $mockSl->shouldReceive('get')->with('LogProcessorManager')->andReturn($mockLogProcessor);
        $sut = new RequestFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(Request::class, $service);
        $this->assertEquals(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Cookie' => 'secureToken=myToken',
                'X-Correlation-Id' => 'IDENT1',
            ],
            $service->getHeaders()->toArray()
        );
    }
}
