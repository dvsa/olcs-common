<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\FstandingFirstVeh;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;

/**
 * FstandingFirstVeh bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FstandingFirstVehTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new FstandingFirstVeh();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetQueryContainsExpectedKeys()
    {
        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->with(\DateTime::W3C)
            ->andReturn('2015-05-01')
            ->once()
            ->getMock()
        );
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

    public function testRenderWithNoFirstVehicleFee()
    {
        $this->sut->setData([]);
        $this->assertEquals('', $this->sut->render());
    }

    public function testRenderWithFirstVehicleFee()
    {
        $this->sut->setData(
            [
                'Count' => 2,
                'Results' => [
                    [
                        'firstVehicleFee' => '123456',
                        'effectiveFrom' => '2015-01-01'
                    ],
                    [
                        'firstVehicleFee' => '023444',
                        'effectiveFrom' => '2013-01-01'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            '123,456',
            $this->sut->render()
        );
    }
}
