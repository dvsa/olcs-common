<?php

/**
 * Fee Listener Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Listener;

use Common\Service\Entity\ApplicationEntityService;
use CommonTest\Bootstrap;
use Common\Service\Listener\FeeListenerService;
use PHPUnit_Framework_TestCase;

/**
 * Fee Listener Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
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
                'getOutstandingContinuationFee'
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

    /**
     * @group listener_services
     * @dataProvider providerEventType
     */
    public function testTriggerPayOrWaiveWithoutApplicationFee($eventType)
    {
        $this->mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue(null));

        $this->setupMaybeContinueLicenceStub(3);

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @group listener_services
     * @dataProvider providerEventType
     */
    public function testTriggerPayOrWaiveWithVariation($eventType)
    {
        $application = array(
            'isVariation' => true
        );

        $this->mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue($application));

        $this->setupMaybeContinueLicenceStub(3);

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @group listener_services
     * @dataProvider providerEventType
     */
    public function testTriggerPayOrWaiveWithoutGrantedApplication($eventType)
    {
        $application = array(
            'isVariation' => false,
            'status' => array(
                'id' => 'FOO'
            )
        );

        $this->mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue($application));

        $this->setupMaybeContinueLicenceStub(3);

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @group listener_services
     * @dataProvider providerEventType
     */
    public function testTriggerPayOrWaiveWithOutstandingFees($eventType)
    {
        $application = array(
            'id' => 7,
            'isVariation' => false,
            'status' => array(
                'id' => ApplicationEntityService::APPLICATION_STATUS_GRANTED
            )
        );

        $fees = array(
            'foo'
        );

        $this->mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue($application));
        $this->mockFeeService->expects($this->once())
            ->method('getOutstandingGrantFeesForApplication')
            ->with(7)
            ->will($this->returnValue($fees));

        $this->setupMaybeContinueLicenceStub(3);

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @group listener_services
     * @dataProvider providerEventType
     */
    public function testTriggerPayOrWaive($eventType)
    {
        $application = array(
            'id' => 7,
            'isVariation' => false,
            'status' => array(
                'id' => ApplicationEntityService::APPLICATION_STATUS_GRANTED
            )
        );

        $fees = array();

        $this->mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue($application));
        $this->mockFeeService->expects($this->once())
            ->method('getOutstandingGrantFeesForApplication')
            ->with(7)
            ->will($this->returnValue($fees));

        $mockProcessor = $this->getMock('\stdClass', ['validateApplication']);
        $mockProcessor->expects($this->once())
            ->method('validateApplication')
            ->with(7);
        $this->sm->setService('Processing\Application', $mockProcessor);

        $this->setupMaybeContinueLicenceStub(3);

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    public function providerEventType()
    {
        return [
            [FeeListenerService::EVENT_WAIVE],
            [FeeListenerService::EVENT_PAY]
        ];
    }

    /**
     * Stub out the maybeContinueLicenceStub method
     * 
     * @param int $feeId Fee ID
     */
    protected function setupMaybeContinueLicenceStub($feeId)
    {
        $feeEntity = [
            'feeType' => [
                'feeType' => [
                    'id' => 'BLANK',
                ]
            ],
        ];
        $this->mockFeeService->expects($this->once())
            ->method('getOverview')->with($feeId)
            ->will($this->returnValue($feeEntity));
    }

    /**
     * Stub out the maybeContinueLicenceStub method
     *
     * @param int $feeId Fee ID
     */
    protected function setupMaybeProcessApplicationFeeStub($feeId)
    {
        $this->mockFeeService->expects($this->once())
            ->method('getApplication')->with($feeId)
            ->will($this->returnValue(null));
    }

    /**
     * @dataProvider providerEventType
     */
    public function testMaybeContinueLicenceNotContFee($eventType)
    {
        $this->setupMaybeProcessApplicationFeeStub(3);

        $feeEntity = [
            'licenceId' => 1966,
            'feeType' => [
                'feeType' => [
                    'id' => 'XX',
                ]
            ],
        ];

        $this->mockFeeService->expects($this->once())
            ->method('getOverview')->with(3)
            ->will($this->returnValue($feeEntity));

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @dataProvider providerEventType
     */
    public function testMaybeContinueLicenceNotContFeeNoOngoingContinuation($eventType)
    {
        $this->setupMaybeProcessApplicationFeeStub(3);

        $mockContinuationDetailService = $this->getMock('\stdClass', ['getOngoingForLicence']);
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $feeEntity = [
            'licenceId' => 1966,
            'feeType' => [
                'feeType' => [
                    'id' => 'CONT',
                ]
            ],
        ];

        $this->mockFeeService->expects($this->once())
            ->method('getOverview')->with(3)
            ->will($this->returnValue($feeEntity));

        $mockContinuationDetailService->expects($this->once())
            ->method('getOngoingForLicence')->with(1966)
            ->will($this->returnValue(false));

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @dataProvider providerEventType
     */
    public function testMaybeContinueLicenceInvalidLicenceStatus($eventType)
    {
        $this->setupMaybeProcessApplicationFeeStub(3);

        $mockContinuationDetailService = $this->getMock('\stdClass', ['getOngoingForLicence']);
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $feeEntity = [
            'licenceId' => 1966,
            'feeType' => [
                'feeType' => [
                    'id' => 'CONT',
                ]
            ],
        ];

        $continuationDetailEntity = [
            'licence' => [
                'status' => [
                    'id' => 'lsts_withdrawn',
                ]
            ]
        ];

        $this->mockFeeService->expects($this->once())
            ->method('getOverview')->with(3)
            ->will($this->returnValue($feeEntity));

        $mockContinuationDetailService->expects($this->once())
            ->method('getOngoingForLicence')->with(1966)
            ->will($this->returnValue($continuationDetailEntity));

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @dataProvider providerEventType
     */
    public function testMaybeContinueLicenceHasOutstandingFees($eventType)
    {
        $this->setupMaybeProcessApplicationFeeStub(3);

        $mockContinuationDetailService = $this->getMock('\stdClass', ['getOngoingForLicence']);
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $feeEntity = [
            'licenceId' => 1966,
            'feeType' => [
                'feeType' => [
                    'id' => 'CONT',
                ]
            ],
        ];

        $continuationDetailEntity = [
            'licence' => [
                'status' => [
                    'id' => 'lsts_valid',
                ]
            ]
        ];

        $this->mockFeeService->expects($this->once())
            ->method('getOverview')->with(3)
            ->will($this->returnValue($feeEntity));

        $mockContinuationDetailService->expects($this->once())
            ->method('getOngoingForLicence')->with(1966)
            ->will($this->returnValue($continuationDetailEntity));

        $this->mockFeeService->expects($this->once())
            ->method('getOutstandingContinuationFee')->with(1966)
            ->will($this->returnValue(['Count' => 1]));

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    /**
     * @dataProvider providerEventType
     */
    public function testMaybeContinueLicenceSuccess($eventType)
    {
        $this->setupMaybeProcessApplicationFeeStub(3);

        $mockContinuationDetailService = $this->getMock('\stdClass', ['getOngoingForLicence']);
        $this->sm->setService('Entity\ContinuationDetail', $mockContinuationDetailService);

        $mockFlashMessenger = $this->getMock('\stdClass', ['addSuccessMessage']);
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        $feeEntity = [
            'licenceId' => 1966,
            'feeType' => [
                'feeType' => [
                    'id' => 'CONT',
                ]
            ],
        ];

        $continuationDetailEntity = [
            'licence' => [
                'status' => [
                    'id' => 'lsts_valid',
                ]
            ]
        ];

        $this->mockFeeService->expects($this->once())
            ->method('getOverview')->with(3)
            ->will($this->returnValue($feeEntity));

        $mockContinuationDetailService->expects($this->once())
            ->method('getOngoingForLicence')->with(1966)
            ->will($this->returnValue($continuationDetailEntity));

        $this->mockFeeService->expects($this->once())
            ->method('getOutstandingContinuationFee')->with(1966)
            ->will($this->returnValue(['Count' => 0]));

        $mockFlashMessenger->expects($this->once())
            ->method('addSuccessMessage')->with('licence.continued.message');

        $this->assertNull($this->sut->trigger(3, $eventType));
    }
}
