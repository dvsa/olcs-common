<?php

/**
 * Fee Id Url formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\FeeIdUrl;
use CommonTest\Bootstrap;

/**
 * Fee Id Url formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeIdUrlTest extends MockeryTestCase
{
    protected $sm;

    protected $mockRouteMatch;

    protected $mockUrlHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->sm = Bootstrap::getServiceManager();

        $this->mockRouteMatch = m::mock('\Zend\Mvc\Router\RouteMatch');
        $this->mockUrlHelper = m::mock();
        $mockRequest = m::mock('\Zend\Stdlib\RequestInterface')
            ->shouldReceive('getQuery')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->once()
                ->andReturn(['foo' => 'bar'])
                ->getMock()
            )
            ->once()
            ->getMock();

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
     */
    public function testFormat()
    {
        $this->mockRouteMatch
            ->shouldReceive('getMatchedRouteName')
            ->andReturn('licence/fee/transaction');

        $this->mockUrlHelper
            ->shouldReceive('fromRoute')
            ->with('licence/fee', ['fee' => 1, 'action' => 'edit-fee'], ['query' => ['foo' => 'bar']], true)
            ->andReturn('the_url');

        $this->assertEquals('<a href="the_url">1</a>', FeeIdUrl::format(['id' => 1], [], $this->sm));
    }
}
