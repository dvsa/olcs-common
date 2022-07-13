<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\CommunityLicenceStatus;
use CommonTest\Bootstrap;

/**
 * Community licence status formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicenceStatusTest extends MockeryTestCase
{
    protected $sm;

    protected $mockRouteMatch;

    protected $mockUrlHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->sm = Bootstrap::getServiceManager();

        $this->mockRouteMatch = m::mock('\Laminas\Mvc\Router\RouteMatch')
            ->shouldReceive('getMatchedRouteName')
            ->andReturn('route')
            ->getMock();

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
     * @dataProvider dataProvider
     */
    public function testFormat($data, $url)
    {
        $this->mockUrlHelper
            ->shouldReceive('fromRoute')
            ->with('route', ['child_id' => $data['id'], 'action' => 'edit'], ['query' => ['foo' => 'bar']], true)
            ->andReturn('the_url')
            ->getMock();

        $this->assertEquals(
            $url,
            CommunityLicenceStatus::format($data, [], $this->sm)
        );
    }

    public function dataProvider()
    {
        return [
            [
                [
                    'id' => 1,
                    'futureSuspension' => [
                        'startDate' => '2017-01-01',
                        'endDate' => '2018-01-01'
                    ],
                    'currentSuspension' => null,
                    'currentWithdrawal' => null
                ],
                '<a class="govuk-link" href="the_url">Suspension due: 01/01/2017 to 01/01/2018</a>'
            ],
            [
                [
                    'id' => 1,
                    'futureSuspension' => [
                        'startDate' => '2017-01-01'
                    ],
                    'currentSuspension' => null,
                    'currentWithdrawal' => null
                ],
                '<a class="govuk-link" href="the_url">Suspension due: 01/01/2017</a>'
            ],
            [
                [
                    'id' => 1,
                    'futureSuspension' => null,
                    'currentSuspension' => [
                        'startDate' => '2016-01-01',
                        'endDate' => '2018-01-01'
                    ],
                    'currentWithdrawal' => null
                ],
                '<a class="govuk-link" href="the_url">Suspended: 01/01/2016 to 01/01/2018</a>'
            ],
            [
                [
                    'id' => 1,
                    'futureSuspension' => null,
                    'currentSuspension' => [
                        'startDate' => '2016-01-01'
                    ],
                    'currentWithdrawal' => null
                ],
                '<a class="govuk-link" href="the_url">Suspended: 01/01/2016</a>'
            ],
            [
                [
                    'id' => 1,
                    'futureSuspension' => null,
                    'currentSuspension' => null,
                    'currentWithdrawal' => [
                        'startDate' => '2016-01-01'
                    ]
                ],
                'Withdrawn: 01/01/2016'
            ],
            [
                [
                    'id' => 1,
                    'status' => [
                        'description' => 'Expired'
                    ],
                    'futureSuspension' => null,
                    'currentSuspension' => null,
                    'currentWithdrawal' => null,
                    'expiredDate' => '2016-01-01',
                ],
                'Expired: 01/01/2016'
            ],
            [
                [
                    'id' => 1,
                    'status' => [
                        'description' => 'Pending'
                    ],
                    'futureSuspension' => null,
                    'currentSuspension' => null,
                    'currentWithdrawal' => null,
                ],
                'Pending'
            ],
        ];
    }
}
