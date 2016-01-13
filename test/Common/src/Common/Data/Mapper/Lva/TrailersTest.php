<?php

/**
 * Trailers Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\Trailers;

/**
 * Trailers Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TrailersTest extends PHPUnit_Framework_TestCase
{
    public function testMapFromResult()
    {
        $data = [
            'organisation' => [
                'confirmShareTrailerInfo' => 'Y'
            ]
        ];

        $expected = [
            'trailers' => [
                'shareInfo' => 'Y'
            ]
        ];

        $this->assertEquals($expected, Trailers::mapFromResult($data));
    }
}
