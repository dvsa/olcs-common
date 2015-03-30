<?php

namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Helper\LicenceStatusHelperService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Class LicenceStatusHelperServiceTest
 * @package CommonTest\Service\Helper
 */
class LicenceStatusHelperServiceTest extends MockeryTestCase
{
    public function testIsLicenceActiveThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $helperService = new LicenceStatusHelperService();
        $helperService->isLicenceActive();
    }

    public function testIsLicenceActiveTrue()
    {
        $comLicEntity = m::mock()->shouldReceive('getValidLicencesForLicenceStatus')
            ->with(1)
            ->andReturn(
                array(
                    'Count' => 2
                )
            )
            ->getMock();

        $busRegEntity = m::mock()->shouldReceive('findByLicenceId')
            ->with(1)
            ->andReturn(
                array(
                    'Results' => array(
                        array('busRegStatus' => 'New'),
                        array('busRegStatus' => 'Registered'),
                        array('busRegStatus' => 'Variation'),
                        array('busRegStatus' => 'Cancellation')
                    )
                )
            )
            ->getMock();

        $applicationEntity = m::mock()->shouldReceive('getApplicationsForLicence')
            ->with(1)
            ->andReturn(
                array(
                    'Results' => array(
                        0 => array('isVariation' => false),
                        1 => array(
                            'isVariation' => true,
                            'status' => array('id' => ApplicationEntityService::APPLICATION_STATUS_UNDER_CONSIDERATION)
                        ),
                        2 => array(
                            'isVariation' => false,
                            'status' => array('id' => ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED)
                        ),
                        3 => array('isVariation' => true),
                    )
                )
            )
            ->getMock();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\CommunityLic')
            ->andReturn($comLicEntity)
            ->shouldReceive('get')
            ->with('Entity\BusReg')
            ->andReturn($busRegEntity)
            ->shouldReceive('get')
            ->with('Entity\Application')
            ->andReturn($applicationEntity)
            ->getMock();

        $helperService = new LicenceStatusHelperService();
        $helperService->setServiceLocator($sm);

        $this->assertTrue($helperService->isLicenceActive(1));

        $this->assertEquals(
            array(
                'communityLicences' => array(
                    'message' => 'There are active, pending or suspended community licences',
                    'result' => true
                ),
                'busRoutes' => array(
                    'message' => 'There are active bus routes on this licence',
                    'result' => true
                ),
                'consideringVariations' => array(
                    'message' => 'There are applications still under consideration',
                    'result' => true
                )
            ),
            $helperService->getMessages()
        );
    }

    /**
     * @dataProvider busRegDataProvider
     */
    public function testIsActiveFalse($busRegData)
    {
        $comLicEntity = m::mock()->shouldReceive('getValidLicencesForLicenceStatus')
        ->with(1)
        ->andReturn(
            array(
                'Count' => 0
            )
        )
        ->getMock();

        $busRegEntity = m::mock()->shouldReceive('findByLicenceId')
            ->with(1)
            ->andReturn($busRegData)
            ->getMock();

        $applicationEntity = m::mock()->shouldReceive('getApplicationsForLicence')
            ->with(1)
            ->andReturn(
                array(
                    'Results' => array()
                )
            )
            ->getMock();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\CommunityLic')
            ->andReturn($comLicEntity)
            ->shouldReceive('get')
            ->with('Entity\BusReg')
            ->andReturn($busRegEntity)
            ->shouldReceive('get')
            ->with('Entity\Application')
            ->andReturn($applicationEntity)
            ->getMock();

        $helperService = new LicenceStatusHelperService();
        $helperService->setServiceLocator($sm);

        $this->assertFalse($helperService->isLicenceActive(1));

        $this->assertEquals(
            array(
                'communityLicences' => false,
                'busRoutes' => false,
                'consideringVariations' => false
            ),
            $helperService->getMessages()
        );
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

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceService)
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($statusRuleService)
            ->getMock();

        $helperService = new LicenceStatusHelperService();
        $helperService->setServiceLocator($sm);

        $helperService->curtailNow(1);
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

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
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
            ->andReturn($tmService)
            ->getMock();

        $helperService = new LicenceStatusHelperService();
        $helperService->setServiceLocator($sm);

        $helperService->revokeNow(1);
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

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceService)
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($statusRuleService)
            ->getMock();

        $helperService = new LicenceStatusHelperService();
        $helperService->setServiceLocator($sm);

        $helperService->suspendNow(1);
    }

    public function testRemoveStatusRulesByLicenceAndTypeThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $helperService = new LicenceStatusHelperService();
        $helperService->removeStatusRulesByLicenceAndType();
    }

    // PROVIDERS

    public function busRegDataProvider()
    {
        return array(
            array(
                false
            ),
            array(
                array(
                    'Results' => array(
                        array(
                            'busRegStatus' => 'INVALID'
                        )
                    )
                )
            )
        );
    }

    public function revocationDataProvider()
    {
        return array(
            array(
                array(
                    'id' => 1,
                    'version' => 1,
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
                    'version' => 1,
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

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($licenceStatusRuleEntity)
            ->getMock();

        $sut = new LicenceStatusHelperService();
        $sut->setServiceLocator($sm);

        $this->assertEquals([['id' => 1]], $sut->getCurrentOrPendingRulesForLicence($licenceId));
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

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($licenceStatusRuleEntity)
            ->getMock();

        $sut = new LicenceStatusHelperService();
        $sut->setServiceLocator($sm);

        $this->assertEquals(true, $sut->hasQueuedRevocationCurtailmentSuspension($licenceId));
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
            ->shouldReceive('save')
            ->once()
            ->with(
                [
                    'id' => $licenceId,
                    'version' => 1,
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

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
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
            ->andReturn($tmService)
            ->getMock();

        $helperService = new LicenceStatusHelperService();
        $helperService->setServiceLocator($sm);

        $helperService->surrenderNow(1, '2015-03-30');
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
            ->shouldReceive('save')
            ->once()
            ->with(
                [
                    'id' => $licenceId,
                    'version' => 1,
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

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
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
            ->andReturn($tmService)
            ->getMock();

        $helperService = new LicenceStatusHelperService();
        $helperService->setServiceLocator($sm);

        $helperService->terminateNow(1, '2015-03-30');
    }
}
