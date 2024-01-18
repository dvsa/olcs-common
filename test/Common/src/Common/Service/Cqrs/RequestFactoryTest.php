<?php

namespace CommonTest\Service\Cqrs;

use Common\Service\Cqrs\RequestFactory;
use Interop\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\Http\Request;
use Olcs\Logging\Log\Processor\RequestId;

class RequestFactoryTest extends TestCase
{
    public function testInvokeWithoutSecureToken(): void
    {
        $cookies = [];

        $mockLogProcessor = m::mock();
        $mockLogProcessor->expects('get')->with(RequestId::class)->andReturn(
            m::mock()->shouldReceive('getIdentifier')->with()->once()->andReturn('IDENT1')->getMock()
        );

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getCookie')->andReturn($cookies);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $mockSl->shouldReceive('get')->with('LogProcessorManager')->andReturn($mockLogProcessor);
        $sut = new RequestFactory();
        $service = $sut->__invoke($mockSl, Request::class);

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

    public function testInvokeWithSecureToken(): void
    {
        $cookies = ['secureToken' => 'myToken'];

        $mockLogProcessor = m::mock();
        $mockLogProcessor->expects('get')->with(RequestId::class)->andReturn(
            m::mock()->shouldReceive('getIdentifier')->with()->once()->andReturn('IDENT1')->getMock()
        );

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getCookie')->andReturn($cookies);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $mockSl->shouldReceive('get')->with('LogProcessorManager')->andReturn($mockLogProcessor);
        $sut = new RequestFactory();
        $service = $sut->__invoke($mockSl, Request::class);

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
