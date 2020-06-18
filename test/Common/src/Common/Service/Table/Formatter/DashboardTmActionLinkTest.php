<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\DashboardTmActionLink;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Formatter\DashboardTmActionLink
 */
class DashboardTmActionLinkTest extends MockeryTestCase
{
    /* @var \Mockery\MockInterface */
    private $mockSm;

    public function setUp(): void
    {
        $this->mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
    }

    public function dataProviderFormat()
    {
        return [
            [
                'statusId' => RefData::TMA_STATUS_AWAITING_SIGNATURE,
                'isVariation' => true,
                'expectTextKey' => 'provide-details',
            ],
            [
                RefData::TMA_STATUS_INCOMPLETE,
                'isVariation' => false,
                'provide-details',
            ],
            [
                RefData::TMA_STATUS_OPERATOR_SIGNED,
                'isVariation' => false,
                'view-details',
            ],
            [
                RefData::TMA_STATUS_POSTAL_APPLICATION,
                'isVariation' => false,
                'view-details',
            ],
            [
                RefData::TMA_STATUS_TM_SIGNED,
                'isVariation' => false,
                'view-details',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFormat($statusId, $isVariation, $expectTextKey)
    {
        $this->mockSm
            ->shouldReceive('get->translate')
            ->once()
            ->with('dashboard.tm-applications.table.action.' . $expectTextKey)
            ->andReturn('EXPECT');

        $this->mockSm
            ->shouldReceive('get->fromRoute')
            ->once()
            ->with(
                (
                    $isVariation
                    ? 'lva-variation/transport_manager_details'
                    : 'lva-application/transport_manager_details'
                ),
                [
                    'action' => null,
                    'application' => 323,
                    'child_id' => 12.,
                ],
                [],
                true
            )
            ->andReturn('http://url.com');

        $data = [
            'applicationId' => 323,
            'transportManagerApplicationStatus' => [
                'id' => $statusId,
                'description' => 'FooBar',
            ],
            'transportManagerApplicationId' => 12,
            'isVariation' => $isVariation,
        ];
        $column = [];

        static::assertEquals(
            '<a href="http://url.com">EXPECT</a>',
            DashboardTmActionLink::format($data, $column, $this->mockSm)
        );
    }
}
