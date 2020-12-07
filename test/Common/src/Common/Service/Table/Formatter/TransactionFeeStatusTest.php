<?php

/**
 * Transaction fee status formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\TransactionFeeStatus as Sut;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Transaction fee status formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionFeeStatusTest extends MockeryTestCase
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
        $mockRequest = m::mock('\Laminas\Stdlib\RequestInterface');
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
    public function testFormat($data, $route, $expectedRouteParams, $expectedOutput)
    {
        $this->mockRouteMatch
            ->shouldReceive('getMatchedRouteName')
            ->andReturn($route);

        $this->mockUrlHelper
            ->shouldReceive('fromRoute')
            ->with($route, $expectedRouteParams, [], true)
            ->andReturn('the_url');

        $this->assertEquals($expectedOutput, Sut::format($data, [], $this->sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'standard' => [
                [
                    'reversingTransaction' => null,
                ],
                null,
                null,
                'Applied',
            ],
            'reversed' => [
                [
                    'reversingTransaction' => [
                        'id' => 99,
                        'type' => RefData::TRANSACTION_TYPE_REVERSAL,
                    ],
                ],
                '/foo/transaction',
                ['transaction' => 99, 'action' => 'edit-fee'],
                '<a href="the_url">Reversed</a>',
            ],
            'refunded' => [
                [
                    'reversingTransaction' => [
                        'id' => 99,
                        'type' => RefData::TRANSACTION_TYPE_REFUND,
                    ],
                ],
                '/foo/transaction',
                ['transaction' => 99, 'action' => 'edit-fee'],
                '<a href="the_url">Refunded</a>',
            ],
            'other' => [
                [
                    'reversingTransaction' => [
                        'id' => 99,
                        'type' => RefData::TRANSACTION_TYPE_OTHER,
                    ],
                ],
                '/foo/transaction',
                ['transaction' => 99, 'action' => 'edit-fee'],
                '<a href="the_url">Adjusted</a>',
            ],
        ];
    }
}
