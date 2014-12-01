<?php

/**
 * Redirect Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Plugin;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Redirect Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RedirectTest extends MockeryTestCase
{
    protected $sut;

    /**
     * @NOTE I am creating a mock of the SUT here, as this class wraps Zends Redirect plugin, and I want to mock it's
     *  default toRoute method
     */
    protected function setUp()
    {
        $this->sut = m::mock('\Common\Controller\Plugin\Redirect')->makePartial();

        $this->mockResponse = m::mock();
    }

    public function testToRouteAjaxWithoutAjax()
    {
        $route = 'foo';
        $params = array('foo' => 'bar');

        $mockController = m::mock();
        $mockController->shouldReceive('getRequest->isXmlHttpRequest')
            ->andReturn(false);

        $this->sut->shouldReceive('getController')
            ->andReturn($mockController)
            ->shouldReceive('toRoute')
            ->with($route, $params, array(), false)
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->toRouteAjax($route, $params));
    }

    public function testToRouteAjaxWithAjax()
    {
        $route = 'foo';
        $params = array('foo' => 'bar');

        $mockResponse = m::mock('\Zend\Http\Response');
        $mockResponse->shouldReceive('getHeaders->addHeaders')
            ->with(['Content-Type' => 'application/json']);

        $mockResponse->shouldReceive('setContent')
            ->with('{"status":302,"location":"URI"}');

        $mockController = m::mock('\Zend\Mvc\Controller\AbstractActionController');
        $mockController->shouldReceive('getRequest->isXmlHttpRequest')
            ->andReturn(true);

        $mockController->shouldReceive('url->fromRoute')
            ->with($route, $params, [], false)
            ->andReturn('URI');

        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getResponse')
            ->andReturn($mockResponse);

        $mockController->shouldReceive('getEvent')
            ->andReturn($mockEvent);

        $this->sut->shouldReceive('getController')
            ->andReturn($mockController)
            ->shouldReceive('toRoute')
            ->with($route, $params, array(), false);

        $this->assertEquals($mockResponse, $this->sut->toRouteAjax($route, $params));
    }
}
