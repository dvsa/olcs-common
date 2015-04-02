<?php

namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Helper\LicenceStatusHelperService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\ApplicationEntityService;
use CommonTest\Bootstrap;

/**
 * Class LicenceStatusHelperServiceTest
 * @package CommonTest\Service\Helper
 */
class LicenceStatusHelperServiceTest extends MockeryTestCase
{
    /**
     * @var LicenceStatusHelperService subject under test
     */
    protected $sut;

    /**
     * @var Mockery\Mock partially mocked service locator
     */
    protected $sm;

    public function setUp()
    {
        $this->sut = new LicenceStatusHelperService();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testIsLicenceActiveThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $helperService = new LicenceStatusHelperService();
        $helperService->isLicenceActive();
    }

    /**
     * @dataProvider isActiveProvider
     */
    public function testIsActive(
        $busRegData,
        $communityLicenceCount,
        $applicationData,
        $expectedResult,
        $expectedMessages
    ) {

        $licenceId = 69;

        $comLicEntity = m::mock()->shouldReceive('getValidLicencesForLicenceStatus')
            ->with($licenceId)
            ->andReturn(['Count' => $communityLicenceCount])
            ->getMock();

        $busRegEntity = m::mock()->shouldReceive('findByLicenceId')
            ->with($licenceId)
            ->andReturn($busRegData)
            ->getMock();

        $applicationEntity = m::mock()->shouldReceive('getApplicationsForLicence')
            ->with($licenceId)
            ->andReturn($applicationData)
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\CommunityLic')
            ->andReturn($comLicEntity)
            ->shouldReceive('get')
            ->with('Entity\BusReg')
            ->andReturn($busRegEntity)
            ->shouldReceive('get')
            ->with('Entity\Application')
            ->andReturn($applicationEntity);

        $this->assertEquals($expectedResult, $this->sut->isLicenceActive($licenceId));

        $this->assertEquals($expectedMessages, $this->sut->getMessages());
    }

    /**
     * @return array [$busRegData, $communityLicenceCount, $applicationData, $expectedResult, $expectedMessages]
     */
    public function isActiveProvider()
    {
        return [
            [
                [
                    'Results' => [
                        ['busRegStatus' => 'New'],
                        ['busRegStatus' => 'Registered'],
                        ['busRegStatus' => 'Variation'],
                        ['busRegStatus' => 'Cancellation'],
                    ],
                ],
                2,
                [
                    'Results' => [
                        ['isVariation' => false],
                        [
                            'isVariation' => true,
                            'status' => ['id' => ApplicationEntityService::APPLICATION_STATUS_UNDER_CONSIDERATION],
                        ],
                        [
                            'isVariation' => false,
                            'status' => ['id' => ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED],
                        ],
                        ['isVariation' => true],
                    ],
                ],
                true,
                [
                    'communityLicences' => [
                        'message' => 'There are active, pending or suspended community licences',
                        'result' => true,
                    ],
                    'busRoutes' => [
                        'message' => 'There are active bus routes on this licence',
                        'result' => true,
                    ],
                    'consideringVariations' => [
                        'message' => 'There are applications still under consideration',
                        'result' => true,
                    ],
                ],
            ],
            [
                false,
                2,
                [
                    'Results' => [],
                ],
                true,
                [
                    'communityLicences' => [
                        'message' => 'There are active, pending or suspended community licences',
                        'result' => true,
                    ],
                    'busRoutes' => false,
                    'consideringVariations' => false,
                ],
            ],
            [
                [
                    'Results' => [
                        ['busRegStatus' => 'INVALID']
                    ],
                ],
                0,
                [
                    'Results' => []
                ],
                false,
                [
                    'communityLicences' => false,
                    'busRoutes' => false,
                    'consideringVariations' => false
                ],
            ],
            [
                false,
                0,
                [
                    'Results' => []
                ],
                false,
                [
                    'communityLicences' => false,
                    'busRoutes' => false,
                    'consideringVariations' => false,
                ],
            ],
        ];
    }

    public function testCurtailNowWithStatuses()
    {
        $licenceId = 1;

        $licenceService = m::mock()
            ->shouldReceive('setLicenceStatus')
            ->with(
                $licenceId,
                'lsts_curtailed'
            )
            ->andReturnNull()
            ->getMock();

        $statusRuleService = m::mock()
            ->shouldReceive('getStatusesForLicence')
            ->with(
                array(
                    'query' => array(
                        'licence' => $licenceId,
                        'licenceStatus' => 'lsts_curtailed'
                    )
                )
            )->andReturn(
                array(
                    'Count' => 1,
                    'Results' => array(
                        array('id' => 1)
                    )
                )
            )
            ->shouldReceive('removeStatusesForLicence')
            ->with(1)
            ->andReturnNull()
            ->getMock();

        m::mock()->shouldReceive('setLicenceStatus')
            ->with(1, 'lsts_curtailed')
            ->andReturnNull()
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceService)
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($statusRuleService);

        $this->sut->curtailNow(1);
    }

    /**
     * @dataProvider revocationDataProvider
     */
    public function testRevokeNowWithStatuses($revocationData)
    {
        $licenceId = 1;

        $statusRuleService = m::mock()
            ->shouldReceive('getStatusesForLicence')
            ->shouldReceive('removeStatusesForLicence')
            ->with($licenceId)
            ->andReturnNull()
            ->getMock();

        $licenceService = m::mock()
            ->shouldReceive('getRevocationDataForLicence')
            ->with($licenceId)
            ->andReturn($revocationData)
            ->shouldReceive('setLicenceStatus')
            ->with(
                $licenceId,
                'lsts_revoked'
            )
            ->andReturnNull()
            ->getMock();

        $discService = m::mock()
            ->shouldReceive('ceaseDiscs')
            ->with(array(1))
            ->getMock();

        $licenceVehicleService = m::mock()
            ->shouldReceive('removeVehicles')
            ->with(array(1))
            ->getMock();

        $tmService = m::mock()
            ->shouldReceive('deleteForLicence')
            ->with(array(1))
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($statusRuleService)
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceService)
            ->shouldReceive('get')
            ->with('Entity\GoodsDisc')
            ->andReturn($discService)
            ->shouldReceive('get')
            ->with('Entity\LicenceVehicle')
            ->andReturn($licenceVehicleService)
            ->shouldReceive('get')
            ->with('Entity\TransportManagerLicence')
            ->andReturn($tmService);

        $this->sut->revokeNow(1);
    }

    public function testSuspendNowWithStatuses()
    {
        $licenceId = 1;

        $licenceService = m::mock()
            ->shouldReceive('setLicenceStatus')
            ->with(
                $licenceId,
                'lsts_suspended'
            )
            ->andReturnNull()
            ->getMock();

        $statusRuleService = m::mock()
            ->shouldReceive('getStatusesForLicence')
            ->with(
                array(
                    'query' => array(
                        'licence' => $licenceId,
                        'licenceStatus' => 'lsts_suspended'
                    )
                )
            )->andReturn(
                array(
                    'Count' => 1,
                    'Results' => array(
                        array(
                            'id' => 1
                        )
                    )
                )
            )
            ->shouldReceive('removeStatusesForLicence')
            ->with(1)
            ->andReturnNull()
            ->getMock();

        m::mock()->shouldReceive('setLicenceStatus')
            ->with(1, 'lsts_suspended')
            ->andReturnNull()
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceService)
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($statusRuleService);

        $this->sut->suspendNow(1);
    }

    public function testRemoveStatusRulesByLicenceAndTypeThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->sut->removeStatusRulesByLicenceAndType();
    }

    public function testResetToValid()
    {
        $licenceId = 1;

        $licenceService = m::mock()
            ->shouldReceive('forceUpdate')
            ->once()
            ->with(
                $licenceId,
                [
                    'status' => LicenceEntityService::LICENCE_STATUS_VALID,
                    'surrenderedDate' => null,
                ]
            )
            ->andReturnNull()
            ->getMock();

        $statusRuleService = m::mock()
            ->shouldReceive('getStatusesForLicence')
            ->once()
            ->with(
                array(
                    'query' => array(
                        'licence' => $licenceId,
                        'licenceStatus' => array(
                            'lsts_curtailed',
                            'lsts_suspended',
                            'lsts_revoked',
                        )
                    )
                )
            )->andReturn(
                array(
                    'Count' => 1,
                    'Results' => array(
                        array(
                            'id' => 1
                        )
                    )
                )
            )
            ->shouldReceive('removeStatusesForLicence')
            ->once()
            ->with(1)
            ->andReturnNull()
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceService)
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($statusRuleService);

        $this->sut->resetToValid($licenceId);
    }

    public function revocationDataProvider()
    {
        return array(
            array(
                array(
                    'id' => 1,
                    'goodsOrPsv' => array(
                        'id' => 'lcat_gv',
                    ),
                    'licenceVehicles' => array(
                        array(
                            'id' => 1,
                            'goodsDiscs' => array(
                                array('id' => 1)
                            )
                        )
                    ),
                    'tmLicences' => array(
                        array(
                            'id' => 1
                        )
                    )
                )
            ),
            array(
                array(
                    'id' => 1,
                    'goodsOrPsv' => array(
                        'id' => 'lcat_psv',
                    ),
                    'psvDiscs' => array(
                        array(
                            'id' => 1
                        )
                    ),
                    'licenceVehicles' => array(
                        array(
                            'id' => 1,
                        )
                    ),
                    'tmLicences' => array(
                        array(
                            'id' => 1
                        )
                    )
                )
            )
        );
    }

    public function testGetCurrentOrPendingRulesForLicence()
    {
        $licenceId = 99;

        $licenceStatusRuleEntity = m::mock()
            ->shouldReceive('getStatusesForLicence')
            ->with(
                array(
                    'query' => array(
                        'licence' => $licenceId,
                        'deletedDate' => 'NULL',
                        'endProcessedDate' => 'NULL',
                    ),
                )
            )
            ->andReturn(
                array(
                    'Count' => 1,
                    'Results' => array(
                        array('id' => 1)
                    ),
                )
            )
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($licenceStatusRuleEntity);

        $this->assertEquals([['id' => 1]], $this->sut->getCurrentOrPendingRulesForLicence($licenceId));
    }

    public function testHasQueuedRevocationCurtailmentSuspension()
    {
        $licenceId = 99;

        $licenceStatusRuleEntity = m::mock()
            ->shouldReceive('getStatusesForLicence')
            ->with(
                [
                    'query' => [
                        'licence' => $licenceId,
                        'deletedDate' => 'NULL',
                        'startProcessedDate' => 'NULL',
                        'licenceStatus' => [
                            LicenceEntityService::LICENCE_STATUS_CURTAILED,
                            LicenceEntityService::LICENCE_STATUS_SUSPENDED,
                            LicenceEntityService::LICENCE_STATUS_REVOKED,
                        ],
                    ],
                ]
            )
            ->andReturn(
                array(
                    'Count' => 1,
                    'Results' => array(
                        array('id' => 1)
                    ),
                )
            )
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($licenceStatusRuleEntity);

        $this->assertEquals(true, $this->sut->hasQueuedRevocationCurtailmentSuspension($licenceId));
    }

    /**
     * @dataProvider revocationDataProvider
     */
    public function testSurrenderNow($revocationData)
    {
        $licenceId = 1;

        $licenceService = m::mock()
            ->shouldReceive('getRevocationDataForLicence')
            ->once()
            ->with($licenceId)
            ->andReturn($revocationData)
            ->shouldReceive('forceUpdate')
            ->once()
            ->with(
                $licenceId,
                [
                    'status' => LicenceEntityService::LICENCE_STATUS_SURRENDERED,
                    'surrenderedDate' => '2015-03-30',
                ]
            )
            ->getMock();

        $discService = m::mock()
            ->shouldReceive('ceaseDiscs')
            ->once()
            ->with(array(1))
            ->getMock();

        $licenceVehicleService = m::mock()
            ->shouldReceive('removeVehicles')
            ->once()
            ->with(array(1))
            ->getMock();

        $tmService = m::mock()
            ->shouldReceive('deleteForLicence')
            ->once()
            ->with(array(1))
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceService)
            ->shouldReceive('get')
            ->with('Entity\GoodsDisc')
            ->andReturn($discService)
            ->shouldReceive('get')
            ->with('Entity\LicenceVehicle')
            ->andReturn($licenceVehicleService)
            ->shouldReceive('get')
            ->with('Entity\TransportManagerLicence')
            ->andReturn($tmService);

        $this->sut->surrenderNow(1, '2015-03-30');
    }

    /**
     * @dataProvider revocationDataProvider
     */
    public function testTerminateNow($revocationData)
    {
        $licenceId = 1;

        $licenceService = m::mock()
            ->shouldReceive('getRevocationDataForLicence')
            ->once()
            ->with($licenceId)
            ->andReturn($revocationData)
            ->shouldReceive('forceUpdate')
            ->once()
            ->with(
                $licenceId,
                [
                    'status' => LicenceEntityService::LICENCE_STATUS_TERMINATED,
                    'surrenderedDate' => '2015-03-30',
                ]
            )
            ->getMock();

        $discService = m::mock()
            ->shouldReceive('ceaseDiscs')
            ->once()
            ->with(array(1))
            ->getMock();

        $licenceVehicleService = m::mock()
            ->shouldReceive('removeVehicles')
            ->once()
            ->with(array(1))
            ->getMock();

        $tmService = m::mock()
            ->shouldReceive('deleteForLicence')
            ->once()
            ->with(array(1))
            ->getMock();

        $this->sm
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceService)
            ->shouldReceive('get')
            ->with('Entity\GoodsDisc')
            ->andReturn($discService)
            ->shouldReceive('get')
            ->with('Entity\LicenceVehicle')
            ->andReturn($licenceVehicleService)
            ->shouldReceive('get')
            ->with('Entity\TransportManagerLicence')
            ->andReturn($tmService);

        $this->sut->terminateNow(1, '2015-03-30');
    }
}
