<?php

/**
 * Bus Registration Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\BusRegEntityService;

/**
 * Bus Registration Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BusRegEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new BusRegEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifierWithResult()
    {
        $response = [
            'Count' => 1,
            'Results' => [
                'RESPONSE'
            ]
        ];
        $params = [
            'regNo' => 123,
            'sort' => 'variationNo',
            'order' => 'DESC'
        ];
        $this->expectOneRestCall('BusReg', 'GET', $params)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->findByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifierWithNoResult()
    {
        $response = [
            'Count' => 0,
            'Results' => []
        ];
        $params = [
            'regNo' => 123,
            'sort' => 'variationNo',
            'order' => 'DESC'
        ];
        $this->expectOneRestCall('BusReg', 'GET', $params)
            ->will($this->returnValue($response));

        $this->assertEquals(false, $this->sut->findByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testFindMostRecentByIdentifierWithResult()
    {
        $response = [
            'Count' => 1,
            'Results' => [
                'RESPONSE'
            ]
        ];
        $params = [
            'regNo' => 123,
            'sort' => 'id',
            'order' => 'DESC'
        ];
        $this->expectOneRestCall('BusReg', 'GET', $params)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->findMostRecentByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testFindMostRecentByIdentifierWithNoResult()
    {
        $response = [
            'Count' => 0,
            'Results' => []
        ];
        $params = [
            'regNo' => 123,
            'sort' => 'id',
            'order' => 'DESC'
        ];
        $this->expectOneRestCall('BusReg', 'GET', $params)
            ->will($this->returnValue($response));

        $this->assertEquals(false, $this->sut->findMostRecentByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForTasks()
    {
        $id = 4;

        $this->expectOneRestCall('BusReg', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForTasks($id));
    }
}
