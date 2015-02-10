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

/**
 * Abstract Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class AbstractFinancialEvidenceAdapterTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock('Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter')->makePartial();
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
}
