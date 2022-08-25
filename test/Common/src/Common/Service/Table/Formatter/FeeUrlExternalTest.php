<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\FeeUrlExternal;
use CommonTest\Bootstrap;

/**
 * Fee Url External formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeUrlExternalTest extends MockeryTestCase
{
    protected $sm;

    protected $mockRouteMatch;

    protected $mockUrlHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->sm = Bootstrap::getServiceManager();

        $this->mockRouteMatch = m::mock('\Laminas\Mvc\Router\RouteMatch');
        $this->mockUrlHelper = m::mock();
        $mockRequest = m::mock('\Laminas\Stdlib\RequestInterface')
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
     *
     * @group Formatters
     * @group FeeStatusFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $routeMatch, $expectedRoute, $expectedRouteParams, $expectedLink, $expectedUrl)
    {
        $this->mockRouteMatch
            ->shouldReceive('getMatchedRouteName')
            ->andReturn($routeMatch);

        $this->mockUrlHelper
            ->shouldReceive('fromRoute')
            ->with($expectedRoute, $expectedRouteParams, ['query' => ['foo' => 'bar']], true)
            ->andReturn($expectedUrl);

        $this->assertEquals($expectedLink, FeeUrlExternal::format($data, [], $this->sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'dashboard fee link' => [
                [
                    'id' => '99',
                    'description' => 'my fee',
                ],
                'fees',
                'fees/pay',
                ['fee' => '99'],
                '<a class="govuk-link" href="feeurl">my fee</a>',
                'feeurl'
            ],
            'dashboard late fee link' => [
                [
                    'id' => '99',
                    'description' => 'my fee',
                    'isExpiredForLicence' => 1
                ],
                'fees',
                'fees/late',
                ['fee' => '99'],
                '<a class="govuk-link" href="lateurl">my fee</a>',
                'lateurl'
            ]
        ];
    }
}
