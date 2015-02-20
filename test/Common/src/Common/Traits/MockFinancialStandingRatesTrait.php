<?php

/**
 * Mock Financial Standing Rates Trait
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Traits;

/**
 * Mock Financial Standing Rates Trait
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
trait MockFinancialStandingRatesTrait
{
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
