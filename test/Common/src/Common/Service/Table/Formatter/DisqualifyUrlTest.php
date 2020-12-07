<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\DisqualifyUrl;
use CommonTest\Bootstrap;

/**
 * Disqualify Url formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DisqualifyUrlTest extends MockeryTestCase
{
    protected $sm;

    protected $mockRouteMatch;

    protected $mockUrlHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->sm = Bootstrap::getServiceManager();

        $this->mockRouteMatch = m::mock(\Laminas\Mvc\Router\RouteMatch::class);
        $this->mockUrlHelper = m::mock();
        $mockRequest = m::mock(\Laminas\Stdlib\RequestInterface::class)
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
     * @dataProvider provider
     */
    public function testFormat($data, $routeMatch, $expectedRoute, $expectedRouteParams, $params, $expectedLink)
    {
        $this->mockRouteMatch
            ->shouldReceive('getMatchedRouteName')
            ->andReturn($routeMatch)
            ->once()
            ->shouldReceive('getParams')
            ->andReturn($params)
            ->once();

        if ($expectedRoute !== null) {
            $this->mockUrlHelper
                ->shouldReceive('fromRoute')
                ->with($expectedRoute, $expectedRouteParams, ['query' => ['foo' => 'bar']], true)
                ->andReturn('the_url');
        }

        $this->assertEquals($expectedLink, DisqualifyUrl::format($data, [], $this->sm));
    }

    public function provider()
    {
        return [
            'licence' => [
                [
                    'id' => '99',
                    'disqualificationStatus' => 'foo',
                ],
                'lva-licence/people',
                'disqualify-person/licence',
                ['person' => '99', 'licence' => 1],
                ['licence' => 1],
                '<a href="the_url" class="js-modal-ajax">foo</a>',
            ],
            'application' => [
                [
                    'id' => '99',
                    'disqualificationStatus' => 'foo',
                ],
                'lva-application/people',
                'disqualify-person/application',
                ['person' => '99', 'application' => 2],
                ['application' => 2],
                '<a href="the_url" class="js-modal-ajax">foo</a>',
            ],
            'variation' => [
                [
                    'id' => '99',
                    'disqualificationStatus' => 'foo',
                ],
                'lva-variation/people',
                'disqualify-person/variation',
                ['person' => '99', 'variation' => 3],
                ['application' => 3],
                '<a href="the_url" class="js-modal-ajax">foo</a>',
            ],
            'unknown' => [
                [
                    'id' => '99',
                    'disqualificationStatus' => 'foo',
                ],
                'bar',
                null,
                null,
                null,
                '<a href="" class="js-modal-ajax">foo</a>',
            ],
        ];
    }
}
