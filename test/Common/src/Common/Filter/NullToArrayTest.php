<?php

namespace CommonTest\Filter;

use Common\Filter\NullToArray;

/**
 * Class NullToArrayTest
 * @package CommonTest\Filter
 * @covers \Common\Filter\NullToFloat
 */
class NullToArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getValueDataProvider
     *
     * @param $input
     * @param $expected
     */
    public function testFilter($input, $expected)
    {
        $filter = new NullToArray();
        $this->assertEquals($expected, $filter->filter($input));
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return [
            'Bool value should return bool'           => [false, false],
            'Null value should return empty array'    => [null, []],
            'Integer value should return same number' => [1, 1],
            'String should return a string'           => ['string', 'string'],
            'Array should return a array'             => [
                ['a' => 'b'],
                ['a' => 'b'],
            ],
        ];
    }
}
