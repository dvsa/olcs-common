<?php

/**
 * AbstractLva Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

/**
 * AbstractLva Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractLvaEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = $this->getMockForAbstractClass('\Common\Service\Entity\AbstractLvaEntityService');

        parent::setUp();
    }

    public function testGetById()
    {
        $id = 3;

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById($id));
    }

    /**
     * @group entity_services
     */
    public function testGetOperatingCentresData()
    {
        $id = 7;

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getOperatingCentresData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetTotalVehicleAuthorisation()
    {
        $id = 7;
        $type = '';

        $response = array(
            'totAuthVehicles' => 12,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 4,
            'totAuthLargeVehicles' => 5
        );

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(12, $this->sut->getTotalVehicleAuthorisation($id, $type));
    }

    /**
     * @group entity_services
     */
    public function testGetTotalVehicleAuthorisationSmall()
    {
        $id = 7;
        $type = 'Small';

        $response = array(
            'totAuthVehicles' => 12,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 4,
            'totAuthLargeVehicles' => 5
        );

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(3, $this->sut->getTotalVehicleAuthorisation($id, $type));
    }

    /**
     * @group entity_services
     */
    public function testGetTotalVehicleAuthorisationMedium()
    {
        $id = 7;
        $type = 'Medium';

        $response = array(
            'totAuthVehicles' => 12,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 4,
            'totAuthLargeVehicles' => 5
        );

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(4, $this->sut->getTotalVehicleAuthorisation($id, $type));
    }

    /**
     * @group entity_services
     */
    public function testGetTotalVehicleAuthorisationLarge()
    {
        $id = 7;
        $type = 'Large';

        $response = array(
            'totAuthVehicles' => 12,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 4,
            'totAuthLargeVehicles' => 5
        );

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(5, $this->sut->getTotalVehicleAuthorisation($id, $type));
    }

    /**
     * @group entity_services
     */
    public function testGetDocuments()
    {
        $id = 3;
        $categoryId = 2;
        $documentSubCategoryId = 5;

        $response = array(
            'documents' => 'RESPONSE'
        );

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue($response));

        $this->setEntity('Foo');

        $this->assertEquals('RESPONSE', $this->sut->getDocuments($id, $categoryId, $documentSubCategoryId));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForVehiclesPsv()
    {
        $id = 4;

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForVehiclesPsv($id));
    }
}
