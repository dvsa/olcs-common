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

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);

        $this->sut = new FeeListenerService();
        $this->sut->setServiceLocator($this->sm);
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
        $mockFeeService = $this->getMock('\stdClass', ['getApplication']);
        $mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue(null));

        $this->sm->setService('Entity\Fee', $mockFeeService);

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

        $mockFeeService = $this->getMock('\stdClass', ['getApplication']);
        $mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue($application));

        $this->sm->setService('Entity\Fee', $mockFeeService);

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

        $mockFeeService = $this->getMock('\stdClass', ['getApplication']);
        $mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue($application));

        $this->sm->setService('Entity\Fee', $mockFeeService);

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

        $mockFeeService = $this->getMock('\stdClass', ['getApplication', 'getOutstandingFeesForApplication']);
        $mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue($application));
        $mockFeeService->expects($this->once())
            ->method('getOutstandingFeesForApplication')
            ->with(7)
            ->will($this->returnValue($fees));

        $this->sm->setService('Entity\Fee', $mockFeeService);

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

        $mockFeeService = $this->getMock('\stdClass', ['getApplication', 'getOutstandingFeesForApplication']);
        $mockFeeService->expects($this->once())
            ->method('getApplication')
            ->will($this->returnValue($application));
        $mockFeeService->expects($this->once())
            ->method('getOutstandingFeesForApplication')
            ->with(7)
            ->will($this->returnValue($fees));

        $this->sm->setService('Entity\Fee', $mockFeeService);

        $mockProcessor = $this->getMock('\stdClass', ['validateApplication']);
        $mockProcessor->expects($this->once())
            ->method('validateApplication')
            ->with(7);
        $this->sm->setService('Processing\Application', $mockProcessor);

        $this->assertNull($this->sut->trigger(3, $eventType));
    }

    public function providerEventType()
    {
        return [
            [FeeListenerService::EVENT_WAIVE],
            [FeeListenerService::EVENT_PAY]
        ];
    }
}
