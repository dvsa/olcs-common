<?php

namespace CommonTest\Service\Cqrs;

use Common\Service\Cqrs\RequestFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RequestFactoryTest
 * @package CommonTest\Service\Cqrs
 */
class RequestFactoryTest extends TestCase
{
    public function testCreateServiceWithoutSecureToken()
    {
        $cookies = [];

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getCookie')->andReturn($cookies);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $sut = new RequestFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(Request::class, $service);
        $this->assertEquals(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            $service->getHeaders()->toArray()
        );
    }

    public function testCreateServiceWithSecureToken()
    {
        $cookies = ['secureToken' => 'myToken'];

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getCookie')->andReturn($cookies);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Request')->andReturn($mockRequest);
        $sut = new RequestFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(Request::class, $service);
        $this->assertEquals(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Cookie' => 'secureToken=myToken'
            ],
            $service->getHeaders()->toArray()
        );
    }
}
