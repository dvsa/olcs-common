<?php

/**
 * Licence Operating Centre Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

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
}
