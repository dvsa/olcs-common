<?php

/**
 * Companies House Alert Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
use Common\Service\Entity\CompaniesHouseAlertEntityService;

/**
 * Companies House Alert Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseAlertEntityServiceTest extends AbstractEntityServiceTestCase
{
    public function setUp()
    {
        $this->sut = new CompaniesHouseAlertEntityService();

        parent::setUp();
    }

    public function testSaveNew()
    {
        $data = [
            'companyNumber' => '01234567',
        ];

        $expectedData = [
            'companyNumber' => '01234567',
            '_OPTIONS_' => [
                'cascade' => [
                    'list' => [
                        'reasons' => [
                            'entity' => 'companiesHouseAlertReason',
                            'parent' => 'companiesHouseAlert',
                        ],
                    ],
                ],
            ],
        ];

        // expectations
        $this->expectOneRestCall('CompaniesHouseAlert', 'POST', $expectedData)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->saveNew($data));
    }
}
