<?php

/**
 * Fee Listener Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Listener;

use Common\Service\Entity\ApplicationEntityService;
use CommonTest\Bootstrap;
use Common\Service\Listener\FeeListenerService;
use PHPUnit_Framework_TestCase;
use Common\Service\Data\FeeTypeDataService;

/**
 * Fee Listener Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeListenerServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;
    protected $sm;
    protected $mockFeeService;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);

        $this->sut = new FeeListenerService();
        $this->sut->setServiceLocator($this->sm);

        $this->mockFeeService = $this->getMock(
            '\stdClass',
            [
                'getApplication',
                'getOverview',
                'getOutstandingGrantFeesForApplication',
                'getOutstandingContinuationFee',
                'getFeeDetailsForInterim'
            ]
        );
        $this->sm->setService('Entity\Fee', $this->mockFeeService);
    }

    /**
     * @group listener_services
     *
     * @expectedException \Common\Service\Listener\Exception
     * @expectedExceptionMessage Event type not found
     */
    public function testTriggerWithInvalidEvent()
    {
        $this->sut->trigger(3, 'FAKE');
    }

    public function providerEventType()
    {
        return [
            [FeeListenerService::EVENT_WAIVE],
            [FeeListenerService::EVENT_PAY]
        ];
    }

    /**
     * Stub out the maybeProcessGrantingFee method
     *
     * @param int $feeId Fee ID
     * @param array $data
     */
    protected function setupMaybeProcessGrantingFee($feeId, $data = null)
    {
        $this->mockFeeService->expects($this->once())
            ->method('getFeeDetailsForInterim')->with($feeId)
            ->will($this->returnValue($data));
    }

    /**
     * @group listener_services
     * @dataProvider providerEventType
     */
    public function testTriggerPayOrWaiveWithNoInterimType($eventType)
    {
        $feeEntity = [
            'licenceId' => 1966,
            'feeType' => [
                'feeType' => [
                    'id' => FeeTypeDataService::FEE_TYPE_GRANTINT,
                ]
            ],
        ];

        $this->setupMaybeProcessGrantingFee(3, $feeEntity);

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @group listener_services
     * @dataProvider providerEventType
     */
    public function testTriggerPayOrWaiveWithInterimTypeGranted($eventType)
    {
        $feeEntity = [
            'licenceId' => 1966,
            'feeType' => [
                'feeType' => [
                    'id' => FeeTypeDataService::FEE_TYPE_GRANTINT,
                ]
            ],
            'application' => [
                'interimStatus' => [
                    'id' => ApplicationEntityService::INTERIM_STATUS_GRANTED
                ],
                'id' => 48
            ]
        ];

        $this->setupMaybeProcessGrantingFee(3, $feeEntity);

        $mockInterimHelper = $this->getMock('\stdClass', ['grantInterim']);
        $mockInterimHelper->expects($this->once())
            ->method('grantInterim')->with(48);

        $this->sm->setService('Helper\Interim', $mockInterimHelper);

        $this->assertNull($this->sut->trigger(3, $eventType));
    }
}
