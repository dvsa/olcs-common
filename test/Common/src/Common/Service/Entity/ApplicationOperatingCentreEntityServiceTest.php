<?php

/**
 * ApplicationOperatingCentre Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
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

    /**
     * Test get all for inspection request
     *
     * @group applicationOperatingCentre
     */
    public function testGetAllForInspectionRequest()
    {
        $query = [
            'application' => 1,
            'action' => '!= D',
            'limit' => 'all'
        ];
        $bundle = [
            'children' => [
                'operatingCentre' => [
                    'children' => [
                        'address'
                    ]
                ],
                'application'
            ]
        ];

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('ApplicationOperatingCentre', 'GET', $query, $bundle)
            ->will($this->returnValue($response));

        $this->assertEquals(['Results' => 'RESPONSE'], $this->sut->getAllForInspectionRequest(1));
    }

    public function testGetForSelect()
    {
        $appId = 111;
        $expected = [
            11 => '111 street, foobar, footown',
            33 => '333 street, foobar, footown'
        ];
        $stubbedAocData = [
            'Results' => [
                [
                    'action' => 'A',
                    'operatingCentre' => [
                        'id' => 11,
                        'address' => [
                            'addressLine1' => '111 street',
                            'addressLine2' => 'foobar',
                            'addressLine3' => 'ignored',
                            'town' => 'footown'
                        ]
                    ]
                ],
                [
                    'action' => 'D',
                    'operatingCentre' => [
                        'id' => 22,
                        'address' => [
                            'addressLine1' => '222 street',
                            'addressLine2' => 'foobar',
                            'addressLine3' => 'ignored',
                            'town' => 'footown'
                        ]
                    ]
                ]
            ]
        ];

        $stubbedLocData = [
            'Results' => [
                [
                    'operatingCentre' => [
                        'id' => 22,
                        'address' => [
                            'addressLine1' => '222 street',
                            'addressLine2' => 'foobar',
                            'addressLine3' => 'ignored',
                            'town' => 'footown'
                        ]
                    ]
                ],
                [
                    'action' => 'D',
                    'operatingCentre' => [
                        'id' => 33,
                        'address' => [
                            'addressLine1' => '333 street',
                            'addressLine2' => 'foobar',
                            'addressLine3' => 'ignored',
                            'town' => 'footown'
                        ]
                    ]
                ]
            ]
        ];

        // Mocks
        $mockApplication = m::mock();
        $mockLoc = m::mock();
        $this->sm->setService('Entity\Application', $mockApplication);
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLoc);

        // Expectations
        $this->expectOneRestCall('ApplicationOperatingCentre', 'GET', ['application' => $appId, 'limit' => 'all'])
            ->will($this->returnValue($stubbedAocData));

        $mockApplication->shouldReceive('getLicenceIdForApplication')
            ->with(111)
            ->andReturn(222);

        $mockLoc->shouldReceive('getOperatingCentresForLicence')
            ->with(222)
            ->andReturn($stubbedLocData);

        // Assertions
        $response = $this->sut->getForSelect($appId);

        $this->assertEquals($expected, $response);
    }
}
