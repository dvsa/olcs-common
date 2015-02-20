<?php

/**
 * Application Tracking Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ApplicationTrackingEntityService;
use CommonTest\Bootstrap;

/**
 * Application Tracking Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationTrackingEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ApplicationTrackingEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     * @expectedExceptionMessage Tracking status not found
     */
    public function testGetTrackingStatusesWithNoRecord()
    {
        $applicationId = 3;

        $data = array(
            'Count' => 0
        );

        $this->expectOneRestCall('ApplicationTracking', 'GET', ['application' => $applicationId])
            ->will($this->returnValue($data));

        $this->sut->getTrackingStatuses($applicationId);
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     * @expectedExceptionMessage Too many tracking statuses found
     */
    public function testGetTrackingStatusesWithTooManyRecords()
    {
        $applicationId = 3;

        $data = array(
            'Count' => 2
        );

        $this->expectOneRestCall('ApplicationTracking', 'GET', ['application' => $applicationId])
            ->will($this->returnValue($data));

        $this->sut->getTrackingStatuses($applicationId);
    }

    /**
     * @group entity_services
     */
    public function testGetTrackingStatuses()
    {
        $applicationId = 3;

        $expected = array(
            'sample' => 'result'
        );

        $data = array(
            'Count' => 1,
            'Results' => array(
                $expected
            )
        );

        $this->expectOneRestCall('ApplicationTracking', 'GET', ['application' => $applicationId])
            ->will($this->returnValue($data));

        $this->assertEquals($expected, $this->sut->getTrackingStatuses($applicationId));
    }

    public function testGetValueOptions()
    {
        $expected = [
            0 => '',
            1 => 'Accepted',
            2 => 'Not accepted',
            3 => 'Not applicable',
        ];

        $this->assertEquals($expected, $this->sut->getValueOptions());
    }
}
