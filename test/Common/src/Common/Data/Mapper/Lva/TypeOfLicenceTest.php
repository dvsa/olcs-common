<?php

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\TypeOfLicence;

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicenceTest extends PHPUnit_Framework_TestCase
{
    public function testMapFromResult()
    {
        $input = [
            'version' => 111,
            'niFlag' => 'Y',
            'goodsOrPsv' => ['id' => 'lcat_gv'],
            'licenceType' => ['id' => 'ltyp_sn']
        ];

        $output = TypeOfLicence::mapFromResult($input);

        $expected = [
            'version' => 111,
            'type-of-licence' => [
                'operator-location' => 'Y',
                'operator-type' => 'lcat_gv',
                'licence-type' => 'ltyp_sn'
            ]
        ];

        $this->assertEquals($expected, $output);
    }
}
