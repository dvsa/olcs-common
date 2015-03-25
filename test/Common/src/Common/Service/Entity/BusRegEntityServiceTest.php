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
    public function testFindByLicenceIdentifierWithNoResult()
    {
        $response = [
            'Count' => 0,
            'Results' => []
        ];
        $params = [
            'licId' => 123
        ];
        $this->expectOneRestCall('BusRegSearchView', 'GET', $params)
            ->will($this->returnValue($response));

        $this->assertEquals(false, $this->sut->findByLicenceIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testFindByLicenceIdentifierWithResult()
    {
        $response = [
            'Count' => 1,
            'Results' => [
                [
                    'id' => 100
                ]
            ]
        ];
        $params = [
            'licId' => 123
        ];
        $this->expectedRestCallInOrder('BusRegSearchView', 'GET', $params)
            ->will($this->returnValue($response));

        $this->assertEquals($response, $this->sut->findByLicenceIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifierWithResult()
    {
        $response = [
            'Count' => 1,
            'Results' => [
                [
                    'id' => 100
                ]
            ]
        ];
        $params = [
            'regNo' => 123,
            'limit' => 1
        ];
        $this->expectedRestCallInOrder('BusRegSearchView', 'GET', $params)
            ->will($this->returnValue($response));

        $this->expectedRestCallInOrder('BusReg', 'GET', 100)
            ->will($this->returnValue('RESPONSE'));

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
            'limit' => 1
        ];
        $this->expectOneRestCall('BusRegSearchView', 'GET', $params)
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
    public function testFindMostRecentByLicenceWithResult()
    {
        $response = [
            'Count' => 1,
            'Results' => [
                'RESPONSE'
            ]
        ];
        $params = [
            'licence' => 123,
            'sort' => 'routeNo',
            'order' => 'DESC',
            'limit' => 1
        ];
        $this->expectOneRestCall('BusReg', 'GET', $params)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->findMostRecentRouteNoByLicence(123));
    }

    /**
     * @group entity_services
     */
    public function testFindMostRecentByLicenceWithNoResult()
    {
        $response = [
            'Count' => 0,
            'Results' => []
        ];
        $params = [
            'licence' => 123,
            'sort' => 'routeNo',
            'order' => 'DESC',
            'limit' => 1
        ];
        $this->expectOneRestCall('BusReg', 'GET', $params)
            ->will($this->returnValue($response));

        $this->assertEquals(false, $this->sut->findMostRecentRouteNoByLicence(123));
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

    /**
     * @group entity_services
     */
    public function testGetDataForFees()
    {
        $id = 4;

        $this->expectOneRestCall('BusReg', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForFees($id));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForVariation()
    {
        $id = 4;

        $this->expectOneRestCall('BusReg', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForVariation($id));
    }
}
