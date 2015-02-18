<?php

/**
 * Abstract Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace OlcsTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter;
use Common\Service\Entity\LicenceEntityService;
use CommonTest\Bootstrap;
use CommonTest\Traits\MockDateTrait;

/**
 * Abstract Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class AbstractFinancialEvidenceAdapterTest extends MockeryTestCase
{
    use MockDateTrait;

    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock('Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter')->makePartial();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);

        $this->sm->setService(
            'Entity\FinancialStandingRate',
            m::mock()
                ->shouldReceive('getRatesInEffect')
                ->andReturn($this->getFinancialStandingRates())
                ->getMock()
        );
    }

    /**
     * @param string $licenceType
     * @param string $goodsOrPsv
     * @param int $expected
     * @dataProvider firstVehicleRateProvider
     */
    public function testGetFirstVehicleRate($licenceType, $goodsOrPsv, $expected)
    {
        $this->assertEquals($expected, $this->sut->getFirstVehicleRate($licenceType, $goodsOrPsv));
    }

    public function firstVehicleRateProvider()
    {
        return [
            'Goods SN' => ['ltyp_sn', 'lcat_gv' , 7000],
            'Goods SI' => ['ltyp_si', 'lcat_gv' , 7000],
            'Goods R'  => ['ltyp_r' , 'lcat_gv' , 3100],
            'PSV SN'   => ['ltyp_sn', 'lcat_psv', 8000],
            'PSV SI'   => ['ltyp_si', 'lcat_psv', 8000],
            'PSV R'    => ['ltyp_r' , 'lcat_psv', 4100],
        ];
    }

    /**
     * @param string $licenceType
     * @param string $goodsOrPsv
     * @param int $expected
     * @dataProvider additionalVehicleRateProvider
     */
    public function testGetAdditionalVehicleRate($licenceType, $goodsOrPsv, $expected)
    {
        $this->assertEquals($expected, $this->sut->getAdditionalVehicleRate($licenceType, $goodsOrPsv));
    }

    public function additionalVehicleRateProvider()
    {
        return [
            'Goods SN' => ['ltyp_sn', 'lcat_gv' , 3900],
            'Goods SI' => ['ltyp_si', 'lcat_gv' , 3900],
            'Goods R'  => ['ltyp_r' , 'lcat_gv' , 1700],
            'PSV SN'   => ['ltyp_sn', 'lcat_psv', 4900],
            'PSV SI'   => ['ltyp_si', 'lcat_psv', 4900],
            'PSV R'    => ['ltyp_r' , 'lcat_psv', 2700],
        ];
    }

    protected function getFinancialStandingRates()
    {
        return [
            [
                'firstVehicleRate'      => '7000.00',
                'additionalVehicleRate' => '3900.00',
                'effectiveFrom'         => '2015-02-01',
                'goodsOrPsv'            => ['id' => 'lcat_gv'],
                'licenceType'           => ['id' => 'ltyp_sn'],
            ],
            [
                'firstVehicleRate'      => '7000.00',
                'additionalVehicleRate' => '3900.00',
                'effectiveFrom'         => '2015-02-01',
                'goodsOrPsv'            => ['id' => 'lcat_gv'],
                'licenceType'           => ['id' => 'ltyp_si'],
            ],
            [
                'firstVehicleRate'      => '3100.00',
                'additionalVehicleRate' => '1700.00',
                'effectiveFrom'         => '2015-02-01',
                'goodsOrPsv'            => ['id' => 'lcat_gv'],
                'licenceType'           => ['id' => 'ltyp_r'],
            ],
            [
                'firstVehicleRate'      => '8000.00',
                'additionalVehicleRate' => '4900.00',
                'effectiveFrom'         => '2015-02-01',
                'goodsOrPsv'            => ['id' => 'lcat_psv'],
                'licenceType'           => ['id' => 'ltyp_sn'],
            ],
            [
                'firstVehicleRate'      => '8000.00',
                'additionalVehicleRate' => '4900.00',
                'effectiveFrom'         => '2015-02-01',
                'goodsOrPsv'            => ['id' => 'lcat_psv'],
                'licenceType'           => ['id' => 'ltyp_si'],
            ],
            [
                'firstVehicleRate'      => '4100.00',
                'additionalVehicleRate' => '2700.00',
                'effectiveFrom'         => '2015-02-01',
                'goodsOrPsv'            => ['id' => 'lcat_psv'],
                'licenceType'           => ['id' => 'ltyp_r'],
            ],
            [
                // an old rate that maybe didn't get deleted
                'firstVehicleRate'      => '7000.00',
                'additionalVehicleRate' => '3900.00',
                'effectiveFrom'         => '2014-11-01',
                'goodsOrPsv'            => ['id' => 'lcat_gv'],
                'licenceType'           => ['id' => 'ltyp_si'],
            ],
        ];
    }
}
