<?php

/**
 * Application Organisation Person Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ApplicationOrganisationPersonEntityService;

/**
 * Application Organisation Person Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationOrganisationPersonEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ApplicationOrganisationPersonEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetByApplicationAndPersonIdWithNoRecords()
    {
        $appId = 3;
        $personId = 7;

        $response = [
            'Count' => 0
        ];

        $data = [
            'application' => $appId,
            'person' => $personId
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertFalse(
            $this->sut->getByApplicationAndPersonId($appId, $personId)
        );
    }

    /**
     * @group entity_services
     */
    public function testGetByApplicationAndPersonId()
    {
        $appId = 3;
        $personId = 7;

        $response = [
            'Count' => 1,
            'Results' => [
                'foo'
            ]
        ];

        $data = [
            'application' => $appId,
            'person' => $personId
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertEquals('foo', $this->sut->getByApplicationAndPersonId($appId, $personId));
    }

    /**
     * @group entity_services
     */
    public function testGetByApplicationAndOriginalPersonIdWithNoRecords()
    {
        $appId = 3;
        $personId = 7;

        $response = [
            'Count' => 0
        ];

        $data = [
            'application' => $appId,
            'originalPerson' => $personId
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertFalse(
            $this->sut->getByApplicationAndOriginalPersonId($appId, $personId)
        );
    }

    /**
     * @group entity_services
     */
    public function testDeleteByApplicationAndPersonId()
    {
        $appId = 3;
        $personId = 7;

        $response = [
            'Count' => 1,
            'Results' => [
                ['id' => 123]
            ]
        ];

        $data = [
            'application' => $appId,
            'person' => $personId
        ];

        $this->expectedRestCallInOrder('ApplicationOrganisationPerson', 'GET', $data)
            ->will($this->returnValue($response));

        $this->expectedRestCallInOrder('ApplicationOrganisationPerson', 'DELETE', ['id' => 123]);

        $this->sut->deleteByApplicationAndPersonId($appId, $personId);
    }

    /**
     * @group entity_services
     */
    public function testDeleteByApplicationAndOriginalPersonId()
    {
        $appId = 3;
        $personId = 7;

        $response = [
            'Count' => 1,
            'Results' => [
                [
                    'id' => 123,
                    'person' => [
                        'id' => 456
                    ]
                ]
            ]
        ];

        $data = [
            'application' => $appId,
            'originalPerson' => $personId
        ];

        $this->expectedRestCallInOrder('ApplicationOrganisationPerson', 'GET', $data)
            ->will($this->returnValue($response));

        $this->expectedRestCallInOrder('ApplicationOrganisationPerson', 'DELETE', ['id' => 123]);

        $personService = $this->getMock('\stdClass', ['delete']);
        $personService->expects($this->once())
            ->method('delete')
            ->with(456);

        $this->sm->setService('Entity\Person', $personService);

        $this->sut->deleteByApplicationAndOriginalPersonId($appId, $personId);
    }

    public function testVariationCreate()
    {

        $personData = [
            'position' => 'a position'
        ];

        $personService = $this->getMock('\stdClass', ['save']);
        $personService->expects($this->once())
            ->method('save')
            ->with($personData)
            ->willReturn(['id' => 5]);

        $this->sm->setService('Entity\Person', $personService);

        $data = [
            'action' => 'A',
            'organisation' => 1,
            'application' => 2,
            'originalPerson' => null,
            'position' => 'a position',
            'person' => 5
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'POST', $data);

        $this->sut->variationCreate(1, 2, $personData);
    }

    public function testVariationUpdate()
    {

        $personData = [
            'position' => 'a position',
            'id' => 10
        ];

        $expectedData = [
            'position' => 'a position'
        ];

        $personService = $this->getMock('\stdClass', ['save']);
        $personService->expects($this->once())
            ->method('save')
            ->with($expectedData)
            ->willReturn(['id' => 5]);

        $this->sm->setService('Entity\Person', $personService);

        $data = [
            'action' => 'U',
            'organisation' => 1,
            'application' => 2,
            'originalPerson' => 10,
            'position' => 'a position',
            'person' => 5
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'POST', $data);

        $this->sut->variationUpdate(1, 2, $personData);
    }

    public function testVariationDelete()
    {

        $data = [
            'action' => 'D',
            'organisation' => 1,
            'application' => 2,
            'person' => 5
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'POST', $data);

        $this->sut->variationDelete(5, 1, 2);
    }

    /**
     * @group entity_services
     */
    public function testGetAllByApplicationWithNoLimit()
    {
        $id = 7;

        $data = [
            'application' => $id,
            'limit' => 50
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'GET', $data)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllByApplication($id));
    }

    /**
     * @group entity_services
     */
    public function testGetAllByApplicationWithLimit()
    {
        $id = 7;
        $limit = 10;

        $data = [
            'application' => $id,
            'limit' => $limit,
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'GET', $data)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllByApplication($id, $limit));
    }

    /**
     * @group entity_services
     */
    public function testUpdatePersonWithPosition()
    {
        $appData = [
            'id' => 1,
            'version' => 2,
        ];

        $personData = [
            'position' => 'test'
        ];

        $mergedData = [
            'id' => 1,
            'version' => 2,
            'position' => 'test',
            '_OPTIONS_' => [
                'force' => true
            ]
        ];

        $this->expectOneRestCall('ApplicationOrganisationPerson', 'PUT', $mergedData)
            ->will($this->returnValue('RESPONSE'));

        $personService = $this->getMock('\stdClass', ['save']);
        $personService->expects($this->once())
            ->method('save')
            ->with($personData)
            ->willReturn('foo');

        $this->sm->setService('Entity\Person', $personService);

        $this->assertEquals(
            'foo',
            $this->sut->updatePerson($appData, $personData)
        );
    }
}
