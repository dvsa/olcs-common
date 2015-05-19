<?php

/**
 * Fee Url formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\FeeUrl;
use CommonTest\Bootstrap;

/**
 * Fee Url formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeUrlTest extends MockeryTestCase
{
    protected $sm;

    protected $mockRouteMatch;

    protected $mockUrlHelper;

    public function setUp()
    {
        parent::setUp();

        $this->sm = Bootstrap::getServiceManager();

        $this->mockRouteMatch = m::mock('\Zend\Mvc\Router\RouteMatch');
        $this->mockUrlHelper = m::mock();
        $mockRequest = m::mock('\Zend\Stdlib\RequestInterface');
        $mockRouter = m::mock()
            ->shouldReceive('match')
            ->with($mockRequest)
            ->andReturn($this->mockRouteMatch)
            ->getMock();

        $this->sm->setService('router', $mockRouter);
        $this->sm->setService('request', $mockRequest);
        $this->sm->setService('Helper\Url', $this->mockUrlHelper);
    }

    /**
     * Test the format method
     *
     * @group Formatters
     * @group FeeStatusFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $routeMatch, $expectedRoute, $expectedRouteParams, $expectedLink)
    {
        $this->mockRouteMatch
            ->shouldReceive('getMatchedRouteName')
            ->andReturn($routeMatch);

        $this->mockUrlHelper
            ->shouldReceive('fromRoute')
            ->with($expectedRoute, $expectedRouteParams, [], true)
            ->andReturn('the_url');

        $this->assertEquals($expectedLink, FeeUrl::format($data, [], $this->sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'licence fee' => [
                [
                    'id' => '99',
                    'description' => 'licence fee',
                ],
                'licence/fees',
                'licence/fees/fee_action',
                ['fee' => '99', 'action' => 'edit-fee', 'controller' => 'LicenceController'],
                '<a href="the_url" class="js-modal-ajax">licence fee</a>',
            ],
            'application fee' => [
                [
                    'id' => '99',
                    'description' => 'app fee',
                ],
                'lva-application/fees',
                'lva-application/fees/fee_action',
                ['fee' => '99', 'action' => 'edit-fee', 'controller' => 'ApplicationController'],
                '<a href="the_url" class="js-modal-ajax">app fee</a>',
            ],
            'bus reg fee' => [
                [
                    'id' => '99',
                    'description' => 'bus reg fee',
                ],
                'licence/bus-fees',
                'licence/bus-fees/fee_action',
                ['fee' => '99', 'action' => 'edit-fee', 'controller' => 'BusFeesController'],
                '<a href="the_url" class="js-modal-ajax">bus reg fee</a>',
            ],
            'misc fee' => [
                [
                    'id' => '99',
                    'description' => 'misc fee',
                ],
                'admin-dashboard/admin-payment-processing/misc-fees',
                'admin-dashboard/admin-payment-processing/misc-fees/fee_action',
                ['fee' => '99', 'action' => 'edit-fee', 'controller' => 'Admin\PaymentProcessingController'],
                '<a href="the_url" class="js-modal-ajax">misc fee</a>',
            ],
            'dashboard fee link' => [
                [
                    'id' => '99',
                    'description' => 'my fee',
                ],
                'fees',
                'fees/pay',
                ['fee' => '99'],
                '<a href="the_url">my fee</a>',
            ],
        ];
    }
}
