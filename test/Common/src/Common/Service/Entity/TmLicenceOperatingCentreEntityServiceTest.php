<?php

/**
 * TM Licence Operating Centre Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TmLicenceOperatingCentreEntityService;

/**
 * TM Licence Operating Centre Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmLicenceOperatingCentreEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TmLicenceOperatingCentreEntityService();

        parent::setUp();
    }

    /**
     * Test deleteByTmLicence
     * 
     * @group tmLicenceOCEntityService
     */
    public function testDeleteByTmLicence()
    {
        $this->expectOneRestCall('TmLicenceOc', 'DELETE', ['transportManagerLicence' => 1])
            ->will($this->returnValue('RESPONSE'));

        $this->sut->deleteByTmLicence(1);
    }
}
