<?php

/**
 * PreviousLicence Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PreviousLicenceEntityService;

/**
 * PreviousLicence Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PreviousLicenceEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new PreviousLicenceEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetForApplicationAndType()
    {
        $id = 7;
        $prevLicType = 3;

        $data = array(
            'application' => $id,
            'previousLicenceType' => $prevLicType,
            'limit' => 'all'
        );

        $expected = array('foo');
        $response = array('Results' => $expected);

        $this->expectOneRestCall('PreviousLicence', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getForApplicationAndType($id, $prevLicType));
    }

    /**
     * @group entity_services
     */
    public function testGetById()
    {
        $id = 7;

        $this->expectOneRestCall('PreviousLicence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById($id));
    }
}
