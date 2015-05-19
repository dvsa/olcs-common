<?php

/**
 * Companies House Company Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
use Common\Service\Entity\CompaniesHouseCompanyEntityService;

/**
 * Companies House Company Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseCompanyEntityServiceTest extends AbstractEntityServiceTestCase
{
    public function setUp()
    {
        $this->sut = new CompaniesHouseCompanyEntityService();

        parent::setUp();
    }

    public function testGetByCompanyNumber()
    {
        $companyNumber = '01234567';

        $expectedQuery = [
            'companyNumber' => $companyNumber,
        ];

        $expectedBundle = [
            'children' => [
                'officers'
            ],
        ];

        $results = [
            'Count' => 2,
            'Results' => [
                ['COMPANY1'],
                ['COMPANY2']
            ],
        ];

        // expectations
        $this->expectOneRestCall('CompaniesHouseCompany', 'GET', $expectedQuery, $expectedBundle)
            ->will($this->returnValue($results));

        // assertions
        $this->assertEquals(['COMPANY1'], $this->sut->getByCompanyNumber($companyNumber));
    }

    public function testSaveNew()
    {
        $data = [
            'companyNumber' => '01234567',
            'officers' => [
                ['name' => 'Bob'],
                ['name' => 'Dave'],
            ]
        ];

        $expectedData = [
            'companyNumber' => '01234567',
            'officers' => [
                ['name' => 'Bob'],
                ['name' => 'Dave'],
            ],
            '_OPTIONS_' => [
                'cascade' => [
                    'list' => [
                        'officers' => [
                            'entity' => 'companiesHouseOfficer',
                            'parent' => 'company',
                        ]
                    ]
                ]
            ],
        ];

        // expectations
        $this->expectOneRestCall('CompaniesHouseCompany', 'POST', $expectedData)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->saveNew($data));
    }
}
