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

    /**
     * Test delete by TM licence and ids
     * 
     * @group tmLicenceOCEntityService
     */
    public function testDeleteByTmLicAndIds()
    {
        $query = [
            'transportManagerLicence' => 1,
            'operatingCentre' => 'IN ["1", "2"]',
        ];
        $this->expectOneRestCall('TmLicenceOc', 'DELETE', $query)
            ->will($this->returnValue('RESPONSE'));
        $this->sut->deleteByTmLicAndIds(1, [1,2]);
    }

    /**
     * Test get all for TM licence
     * 
     * @group tmLicenceOCEntityService
     */
    public function testGetAllForTmLicence()
    {
        $dataBundle = [
            'children' => [
                'transportManagerLicence',
                'operatingCentre'
            ]
        ];

        $this->expectOneRestCall('TmLicenceOc', 'GET', ['transportManagerLicence' => 1], $dataBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllForTmLicence(1));
    }
}
