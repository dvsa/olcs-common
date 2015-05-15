<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TotalContFee;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\FeeTypeEntityService;
use Common\Service\Entity\TrafficAreaEntityService;

/**
 * FstandingAdditionalVeh bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TotalContFeeTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new TotalContFee();
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
            'niFlag' => 'N',
            'trafficAreaId' => 'X'
        ];
        $query = $this->sut->getQuery($data);

        $this->assertEquals('FeeType', $query['service']);
        $this->assertEquals(
            [
                'feeType' => FeeTypeEntityService::FEE_TYPE_CONTINUATION,
                'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                'licenceType' => [
                    LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                    'NULL'
                ],
                'trafficAreaId' => '!= ' . TrafficAreaEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE,
                'effectiveFrom' => '<= 2015-05-01',
                'sort' => 'effectiveFrom',
                'order' => 'DESC',
                'limit' => 1
            ],
            $query['data']
        );
    }

    public function testRenderWithNoTotalContFee()
    {
        $this->sut->setData(['Results' => []]);
        $this->assertEquals('', $this->sut->render());
    }

    /**
     * @dataProvider resultsProvider
     */
    public function testRenderWithTotalContFee($results)
    {
        $this->sut->setData(
            [
                'Count' => 2,
                'Results' => $results
            ]
        );
        $this->assertEquals(
            '123,456',
            $this->sut->render()
        );
    }

    public function resultsProvider()
    {
        return [
            [
                [
                    [
                        'fixedValue' => '123456',
                        'effectiveFrom' => '2015-01-01'
                    ],
                    [
                        'fixedValue' => '789012',
                        'effectiveFrom' => '2014-01-01'
                    ]
                ]
            ],
            [
                [
                    [
                        'fixedValue' => '0',
                        'fiveYearValue' => '123456',
                        'effectiveFrom' => '2015-01-01'
                    ],
                    [
                        'fixedValue' => '0',
                        'fiveYearValue' => '789012',
                        'effectiveFrom' => '2014-01-01'
                    ]
                ]
            ]
        ];
    }
}
