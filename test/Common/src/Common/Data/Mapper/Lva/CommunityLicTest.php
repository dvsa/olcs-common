<?php

/**
 * Community Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\CommunityLic;

/**
 * Community Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicTest extends PHPUnit_Framework_TestCase
{
    public function testMapFromResult()
    {
        $input = [
            'foo' => 'bar'
        ];

        $output = CommunityLic::mapFromResult($input);

        $expected = [
            'foo' => 'bar'
        ];

        $this->assertEquals($expected, $output);
    }
}
