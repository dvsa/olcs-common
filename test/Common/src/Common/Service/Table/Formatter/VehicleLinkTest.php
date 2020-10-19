<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\TransactionUrl;
use Common\Service\Table\Formatter\VehicleLink;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Fee Id Url formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
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

        $this->mockRouteMatch = m::mock('\Zend\Mvc\Router\RouteMatch');
        $this->mockUrlHelper = m::mock(UrlHelperService::class);


        $mockRequest = m::mock('\Zend\Stdlib\RequestInterface');
        $mockRouter = m::mock()
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

        $this->assertEquals('<a href="the_url">VRM</a>',
            VehicleLink::format(
                [
                    'vehicle' =>
                        [
                            'id' => 1,
                            'vrm' => 'VRM'
                        ]
                ]
                ,
                [],
                $this->sm
            )
        );
    }
}
