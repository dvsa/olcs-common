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
     * @NOTE I am creating a mock of the SUT here, as this class wraps Laminas Redirect plugin, and I want to mock it's
     *  default toRoute method
     */
    protected function setUp(): void
    {
        $this->sut = m::mock('\Common\Controller\Plugin\Redirect')->makePartial();

        $this->mockResponse = m::mock();
    }

    /**
     * @group controller_plugin
     */
    public function testToRouteAjaxWithoutAjax()
    {
        $route = 'foo';
        $params = array('foo' => 'bar');
        $options = ['option_1' => 'val 1'];

        $mockController = m::mock();
        $mockController->shouldReceive('getRequest->isXmlHttpRequest')
            ->andReturn(false);

        $this->sut->shouldReceive('getController')
            ->andReturn($mockController)
            ->shouldReceive('toRoute')
            ->with($route, $params, $options, false)
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->toRouteAjax($route, $params, $options));
    }

    /**
     * @group controller_plugin
     */
    public function testToRouteAjaxWithAjax()
    {
        $route = 'foo';
        $params = array('foo' => 'bar');
        $options = ['fragment' => 'frag'];

        $mockResponse = m::mock('\Laminas\Http\Response');
        $mockResponse->shouldReceive('getHeaders->addHeaders')
            ->with(['Content-Type' => 'application/json']);

        $mockResponse->shouldReceive('setContent')
            ->with('{"status":302,"location":"URI"}');

        $mockController = m::mock('\Laminas\Mvc\Controller\AbstractActionController');
        $mockController->shouldReceive('getRequest->isXmlHttpRequest')
            ->andReturn(true);

        $mockController->shouldReceive('url->fromRoute')
            ->with($route, $params, m::type('array'), false)
            ->andReturnUsing(
                function ($route, $params, $options) {
                    $this->assertNotEmpty($options['query']['reload']);
                    return 'URI';
                }
            );

        $mockEvent = m::mock('\Laminas\Mvc\MvcEvent');
        $mockEvent->shouldReceive('getResponse')
            ->andReturn($mockResponse);

        $mockController->shouldReceive('getEvent')
            ->andReturn($mockEvent);

        $this->sut->shouldReceive('getController')
            ->andReturn($mockController)
            ->shouldReceive('toRoute')
            ->with($route, $params, array(), false);

        $this->assertEquals($mockResponse, $this->sut->toRouteAjax($route, $params, $options));
    }

    /**
     * @group controller_plugin
     */
    public function testRefreshAjax()
    {
        $this->sut->shouldReceive('toRouteAjax')
            ->with(null, [], [], true)
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->refreshAjax());
    }
}
