<?php

/**
 * Organisation Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\OrganisationEntityService;
use Common\Service\Helper\DataHelperService;
use Mockery as m;

/**
 * Organisation Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new OrganisationEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetBusinessDetailsData()
    {
        $id = 3;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getBusinessDetailsData($id));
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifier()
    {
        $this->expectOneRestCall('Organisation', 'GET', 123)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->findByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testGetLicencesByStatus()
    {
        $orgData = [
            'licences' => 'LICENCES'
        ];

        $expectedBundle = [
            'children' => [
                'licences' => [
                    'children' => [
                        'licenceType',
                        'status',
                        'goodsOrPsv',
                    ],
                    'criteria' => [
                        'status' => 'IN ["lsts_valid","lsts_suspended","lsts_curtailed"]'
                    ],
                ],
            ],
        ];
        $this->expectOneRestCall('Organisation', 'GET', 123, $expectedBundle)
            ->will($this->returnValue($orgData));

        $this->assertEquals(
            'LICENCES',
            $this->sut->getLicencesByStatus(
                123,
                [
                    'lsts_valid',
                    'lsts_suspended',
                    'lsts_curtailed',
                ]
            )
        );
    }

    /**
     * @group entity_services
     * @dataProvider provideIsMlhTestData
     */
    public function testIsMlh($validLicences, $expected)
    {
        $orgData = [
            'licences' => $validLicences
        ];

        $expectedBundle = [
            'children' => [
                'licences' => [
                    'children' => [
                        'licenceType',
                        'status',
                        'goodsOrPsv',
                    ],
                    'criteria' => [
                        'status' => 'IN ["lsts_valid"]'
                    ],
                ],
            ],
        ];
        $this->expectOneRestCall('Organisation', 'GET', 123, $expectedBundle)
            ->will($this->returnValue($orgData));

        $this->assertEquals(
            $expected,
            $this->sut->isMlh(123)
        );
    }

    public function provideIsMlhTestData()
    {
        return [
            [
                [],
                false,
            ],
            [
                ['valid_licence'],
                true
            ]
        ];
    }
}
