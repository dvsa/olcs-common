<?php

/**
 * Transport Manager Licence Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TransportManagerLicenceEntityService;

/**
 * Transport Manager Licence Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerLicenceEntityServiceTest extends AbstractEntityServiceTestCase
{

    protected $dataBundle = [
        'children' => [
            'licence' => [
                'children' => [
                    'organisation',
                    'status'
                ]
            ],
            'transportManager' => [
                'children' => [
                    'tmType'
                ]
            ],
            'tmType',
            'operatingCentres'
        ]
    ];

    protected function setUp()
    {
        $this->sut = new TransportManagerLicenceEntityService();

        parent::setUp();
    }

    /**
     * Test get Transport Manager licence
     *
     * @group transportManagerLicences
     */
    public function testGetTransportManagerLicence()
    {
        $this->expectOneRestCall('TransportManagerLicence', 'GET', 1, $this->dataBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getTransportManagerLicence(1));
    }

    public function testGetByTransportManagerAndLicence()
    {
        $tmId = 123;
        $licenceId = 321;

        $query = [
            'transportManager' => $tmId,
            'licence' => $licenceId
        ];

        $this->expectOneRestCall('TransportManagerLicence', 'GET', $query)
            ->will($this->returnValue(['Results' => 'RESPONSE']));

        $this->assertEquals('RESPONSE', $this->sut->getByTransportManagerAndLicence($tmId, $licenceId));
    }

    public function testDeleteForLicence()
    {
        $licenceId = 123;

        $query = ['licence' => $licenceId];

        $this->expectOneRestCall('TransportManagerLicence', 'DELETE', $query)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->deleteForLicence($licenceId));
    }


    public function testGetByLicenceWithHomeContactDetails()
    {
        $licenceId = 443;
        $query = ['licence' => $licenceId, 'sort' => 'id', 'order' => 'DESC', 'limit' => 'all'];

        $this->expectOneRestCall('TransportManagerLicence', 'GET', $query)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getByLicenceWithHomeContactDetails($licenceId));
    }

    /**
     * @group transportManagerLicenceEntity
     */
    public function testGetTmForLicence()
    {
        $licenceId = 2;
        $query = ['licence' => $licenceId, 'limit' => 'all'];

        $this->expectOneRestCall('TransportManagerLicence', 'GET', $query)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getTmForLicence($licenceId));
    }
}
