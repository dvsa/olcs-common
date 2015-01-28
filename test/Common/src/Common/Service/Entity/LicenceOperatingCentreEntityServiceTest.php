<?php

/**
 * Licence Operating Centre Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
use Common\Service\Entity\LicenceOperatingCentreEntityService;

/**
 * Licence Operating Centre Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentreEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new LicenceOperatingCentreEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetVehicleAuths()
    {
        $id = 3;

        $this->expectOneRestCall('LicenceOperatingCentre', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getVehicleAuths($id));
    }

    public function testVariationDelete()
    {
        $id = 1;
        $appId = 4;
        $stubbedData = [
            'id' => 1,
            'version' => 2,
            'createdOn' => 'blap',
            'lastModifiedOn' => 'blip',
            'operatingCentre' => ['id' => 5],
            'foo' => 'bar'
        ];

        $expectedData = [
            'foo' => 'bar',
            'operatingCentre' => 5,
            'action' => 'D',
            'application' => 4
        ];

        $mockAoc = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockAoc);

        $this->expectOneRestCall('LicenceOperatingCentre', 'GET', 1)
            ->will($this->returnValue($stubbedData));

        $mockAoc->shouldReceive('save')
            ->with($expectedData)
            ->andReturn('SAVED');

        $this->assertEquals('SAVED', $this->sut->variationDelete($id, $appId));
    }

    /**
     * Test GetOperatingCentresForLicence
     *
     * @group licenceOperatingCentreEntity
     */
    public function testGetOperatingCentresForLicence()
    {
        $bundle = [
            'children' => [
                'operatingCentre' => [
                    'children' => [
                        'address'
                    ]
                ]
            ]
        ];
        $this->expectOneRestCall('LicenceOperatingCentre', 'GET', ['licence' => 1], $bundle)
            ->will($this->returnValue('response'));

        $this->assertEquals('response', $this->sut->getOperatingCentresForLicence(1));
    }

    /**
     * Test getAuthorityDataForLicence
     *
     * @group licenceOperatingCentreEntity
     */
    public function testGetAuthorityDataForLicence()
    {
        $bundle = [
            'children' => [
                'operatingCentre'
            ]
        ];
        $this->expectOneRestCall('LicenceOperatingCentre', 'GET', ['licence' => 1], $bundle)
            ->will($this->returnValue('response'));

        $this->assertEquals('response', $this->sut->getAuthorityDataForLicence(1));
    }
}
