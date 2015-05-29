<?php

/**
 * Companies House Alert Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
use Common\Service\Entity\CompaniesHouseAlertEntityService;
use CommonTest\Bootstrap;

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

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);

        parent::setUp();
    }

    public function testSaveNew()
    {
        $data = [
            'companyNumber' => '01234567',
            'reasons' => [
                ['reasonType' => 'foo'],
                ['reasonType' => 'bar'],
            ],
        ];

        $expectedData = [
            'companyNumber' => '01234567',
        ];

        $saved = ['id' => '99'];

        $this->sm->setService(
            'Entity\CompaniesHouseAlertReason',
            m::mock()
                ->shouldReceive('multiCreate')
                ->once()
                ->with(
                    [
                        ['companiesHouseAlert' => 99, 'reasonType' => 'foo'],
                        ['companiesHouseAlert' => 99, 'reasonType' => 'bar']
                    ]
                )
                ->getMock()
        );

        // expectations
        $this->expectOneRestCall('CompaniesHouseAlert', 'POST', $expectedData)
            ->will($this->returnValue($saved));

        $this->assertEquals($saved, $this->sut->saveNew($data));
    }
}
