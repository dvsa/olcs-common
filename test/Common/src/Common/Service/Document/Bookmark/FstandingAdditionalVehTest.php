<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\FstandingAdditionalVeh;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\LicenceEntityService;

/**
 * FstandingAdditionalVeh bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FstandingAdditionalVehTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new FstandingAdditionalVeh();
    }

    public function testGetQueryContainsExpectedKeys()
    {
        $mockDateHelper = m::mock('Common\Service\Helper\DateHelperService')
            ->shouldReceive('getDate')
            ->with('Y-m-d')
            ->andReturn('2015-05-01')
            ->once()
            ->getMock();
        $this->sut->setDateHelper($mockDateHelper);

        $data = [
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
        ];
        $query = $this->sut->getQuery($data);

        $this->assertEquals('FinancialStandingRate', $query['service']);
        $this->assertEquals(
            [
                'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'effectiveFrom' => '<= 2015-05-01',
                'sort' => 'effectiveFrom',
                'order' => 'DESC',
                'limit' => 1
            ],
            $query['data']
        );
    }

    public function testRenderWithNoAdditionalVehicleFee()
    {
        $this->sut->setData([]);
        $this->assertEquals('', $this->sut->render());
    }

    public function testRenderWithAdditionalVehicleFee()
    {
        $this->sut->setData(
            [
                'Count' => 2,
                'Results' => [
                    [
                        'additionalVehicleRate' => '123456',
                        'effectiveFrom' => '2015-01-01'
                    ],
                    [
                        'additionalVehicleRate' => '789012',
                        'effectiveFrom' => '2014-01-01'
                    ],
                ]
            ]
        );

        $this->assertEquals(
            '123,456',
            $this->sut->render()
        );
    }
}
