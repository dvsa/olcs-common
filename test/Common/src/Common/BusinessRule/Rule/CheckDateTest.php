<?php

/**
 * Check Date Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use PHPUnit_Framework_TestCase;
use Common\BusinessRule\Rule\CheckDate;

/**
 * Check Date Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CheckDateTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new CheckDate();
    }

    /**
     * @dataProvider providerValidate
     */
    public function testValidate($input, $expected)
    {
        $this->assertEquals($expected, $this->sut->validate($input));
    }

    public function providerValidate()
    {
        return [
            'invalid month' => [
                [
                    'day' => '1',
                    'month' => '13',
                    'year' => '2014'
                ],
                null
            ],
            'invalid day' => [
                [
                    'day' => '32',
                    'month' => '12',
                    'year' => '2014'
                ],
                null
            ],
            'invalid date' => [
                [
                    'day' => '31',
                    'month' => '02',
                    'year' => '2014'
                ],
                null
            ],
            'valid date' => [
                [
                    'day' => '28',
                    'month' => '02',
                    'year' => '2014'
                ],
                '2014-02-28'
            ]
        ];
    }
}
