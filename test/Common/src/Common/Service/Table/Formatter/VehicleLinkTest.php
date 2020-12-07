<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\VehicleLink;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Mvc\Router\RouteMatch;
use Laminas\Mvc\Router\RouteStackInterface;
use Laminas\Stdlib\RequestInterface;

/**
 * Vehicle Url formatter test
 */
class VehicleLinkTest extends MockeryTestCase
{
    protected $sm;

    protected $mockRouteMatch;

    protected $mockUrlHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->sm = Bootstrap::getServiceManager();

        $this->mockRouteMatch = m::mock(RouteMatch::class);
        $this->mockUrlHelper = m::mock(UrlHelperService::class);

        $mockRequest = m::mock(RequestInterface::class);
        $mockRouter = m::mock(RouteStackInterface::class)
            ->shouldReceive('match')
            ->with($mockRequest)
            ->andReturn($this->mockRouteMatch)
            ->getMock();

        $this->sm->setService('router', $mockRouter);
        $this->sm->setService('Helper\Url', $this->mockUrlHelper);
    }

    /**
     * Test the format method
     */
    public function testFormat()
    {
        $this->mockRouteMatch
            ->shouldReceive('getMatchedRouteName')
            ->withNoArgs()
            ->andReturn('licence/vehicle/view');

        $this->mockUrlHelper
            ->shouldReceive('fromRoute')
            ->with(
                'licence/vehicle/view/GET',
                ['vehicle' => 1],
                [],
                true
            )
            ->andReturn('the_url');

        $this->assertEquals(
            '<a href="the_url">VRM</a>',
            VehicleLink::format(
                [
                    'vehicle' =>
                        [
                            'id' => 1,
                            'vrm' => 'VRM'
                        ]
                ],
                [],
                $this->sm
            )
        );
    }
}
