<?php

/**
 * Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\CompanySubsidiary;

/**
 * Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiaryTest extends PHPUnit_Framework_TestCase
{
    public function testMapFromResult()
    {
        $input = [
            'foo' => 'bar'
        ];

        $output = CompanySubsidiary::mapFromResult($input);

        $expected = [
            'data' => [
                'foo' => 'bar'
            ]
        ];

        $this->assertEquals($expected, $output);
    }
}
