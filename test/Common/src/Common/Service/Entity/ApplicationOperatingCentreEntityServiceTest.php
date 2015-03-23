<?php

/**
 * ApplicationOperatingCentre Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ApplicationOperatingCentreEntityService;

/**
 * ApplicationOperatingCentre Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentreEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ApplicationOperatingCentreEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetForApplication()
    {
        $id = 3;

        $query = array(
            'application' => $id,
            'limit' => 'all'
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('ApplicationOperatingCentre', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getForApplication($id));
    }

    public function testGetByApplicationAndOperatingCentre()
    {
        $id = 3;
        $ocId = 5;

        $query = array(
            'application' => $id,
            'operatingCentre' => $ocId
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('ApplicationOperatingCentre', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getByApplicationAndOperatingCentre($id, $ocId));
    }

    public function testClearInterims()
    {
        $data = [
            [
                'id' => 1,
                'isInterim' => false,
                '_OPTIONS_' => ['force' => true],
            ],
            [
                'id' => 2,
                'isInterim' => false,
                '_OPTIONS_' => ['force' => true],
            ],
            '_OPTIONS_' => [
                'multiple' => true
            ]
        ];
        $this->expectOneRestCall('ApplicationOperatingCentre', 'PUT', $data);

        $this->sut->clearInterims([1, 2]);
    }
}
