<?php

namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

use Common\Service\Helper\LicenceStatusHelperService;

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
        $helperService->isLicenceCurtailable();
    }

    public function testIsLicenceCurtailableTrue()
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
                        1 => array('isVariation' => true),
                        2 => array('isVariation' => false),
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

        $helperService->isLicenceCurtailable(1);

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

    public function testIsLicenceCurtailableFalse()
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
            ->andReturn(
                array(
                    'Results' => array()
                )
            )
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

        $helperService->isLicenceCurtailable(1);

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
        $licenceStatusRuleEntity = m::mock()
            ->shouldReceive('getStatusesForLicence')
            ->with(
                1,
                array(
                    'licenceStatus' => 'lsts_curtailed'
                )
            )
            ->andReturn(
                array(
                'Count' => 1,
                    'Results' => array(
                        'id' => 1
                    )
                )
            )
            ->shouldReceive('removeStatusesForLicence')
            ->getMock();

        $licenceEntity = m::mock()
            ->shouldReceive('forceUpdate')
            ->with(
                1,
                array(
                    'status' => 'lsts_curtailed'
                )
            )
            ->getMock();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\LicenceStatusRule')
            ->andReturn($licenceStatusRuleEntity)
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn($licenceEntity)
            ->getMock();

        $helperService = new LicenceStatusHelperService();
        $helperService->setServiceLocator($sm);

        $helperService->curtailNow(1);
    }
}
