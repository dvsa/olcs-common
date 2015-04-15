<?php

namespace CommonTest\Service\Entity;

use Common\Service\Entity\EbsrSubmissionEntityService;

/**
 * Class EbsrSubmissionEntityServiceTest
 * @package CommonTest\Service\Entity
 */
class EbsrSubmissionEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new EbsrSubmissionEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testFetchByBusRegId()
    {
        $id = 3;

        $arr = [
            'id' => 7,
            'busRegId' => $id
        ];
        $response = [
            'Results' => [
                $arr
            ],
            'Count' => 1
        ];

        $this->expectOneRestCall('EbsrSubmission', 'GET', ['busReg' => $id])
            ->will($this->returnValue($response));

        $this->assertEquals($arr, $this->sut->fetchByBusRegId($id));
    }

    /**
     * @group entity_services
     */
    public function testFetchByBusRegIdNotFound()
    {
        $id = 3;

        $response = [
            'Results' => [],
            'Count' => 0
        ];

        $this->expectOneRestCall('EbsrSubmission', 'GET', ['busReg' => $id])
            ->will($this->returnValue($response));

        $this->assertFalse($this->sut->fetchByBusRegId($id));
    }
}
