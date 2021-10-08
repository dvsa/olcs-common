<?php

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use Common\Data\Mapper\Lva\TypeOfLicence;

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicenceTest extends \PHPUnit\Framework\TestCase
{
    public function testMapFromResult()
    {
        $input = [
            'version' => 111,
            'niFlag' => 'Y',
            'goodsOrPsv' => ['id' => 'lcat_gv'],
            'licenceType' => ['id' => 'ltyp_sn'],
            'vehicleType' => ['id' => 'app_veh_type_mixed']
        ];

        $output = TypeOfLicence::mapFromResult($input);

        $expected = [
            'version' => 111,
            'type-of-licence' => [
                'operator-location' => 'Y',
                'operator-type' => 'lcat_gv',
                'licence-type' => [
                    'licence-type' => 'ltyp_sn',
                    'ltyp_siContent' => [
                        'vehicle-type' => 'app_veh_type_mixed'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $output);
    }
}
