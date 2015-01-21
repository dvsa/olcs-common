<?php

/**
 * TM Application Operating Centre Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
use Common\Service\Entity\TmApplicationOperatingCentreEntityService;

/**
 * TM Application Operating Centre Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmApplicationOperatingCentreEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TmApplicationOperatingCentreEntityService();

        parent::setUp();
    }

    /**
     * Test get all for TM application
     * 
     * @group tmApplicationOCEntityService
     */
    public function testGetAllForTmApplication()
    {
        $dataBundle = [
            'children' => [
                'transportManagerApplication',
                'operatingCentre'
            ]
        ];

        $this->expectOneRestCall('TmApplicationOc', 'GET', ['transportManagerApplication' => 1], $dataBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllForTmApplication(1));
    }

    /**
     * Test get all for TM application
     * 
     * @group tmApplicationOCEntityService
     */
    public function testDeleteByTmAppAndIds()
    {
        $query = [
            'transportManagerApplication' => 1,
            'operatingCentre' => 'IN ["1", "2"]',
            'limit' => 'all'
        ];
        $response = [
            'Results' => [
                ['id' => 1],
                ['id' => 2]
            ]
        ];
        $this->expectedRestCallInOrder('TmApplicationOc', 'GET', $query)
            ->will($this->returnValue($response));

        $this->expectedRestCallInOrder('TmApplicationOc', 'DELETE', ['id' => 1]);
        $this->expectedRestCallInOrder('TmApplicationOc', 'DELETE', ['id' => 2]);

        $this->sut->deleteByTmAppAndIds(1, [1,2]);
    }

    /**
     * Test deleteByTmApplication
     * 
     * @group tmApplicationOCEntityService
     */
    public function testDeleteByTmApplication()
    {
        $query = [
            'transportManagerApplication' => 1,
            'limit' => 'all'
        ];
        $response = [
            'Results' => [
                ['id' => 1],
            ]
        ];
        $this->expectedRestCallInOrder('TmApplicationOc', 'GET', $query)
            ->will($this->returnValue($response));

        $this->expectedRestCallInOrder('TmApplicationOc', 'DELETE', ['id' => 1]);

        $this->sut->deleteByTmApplication(1);
    }
}
