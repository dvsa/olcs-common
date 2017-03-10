<?php

namespace CommonTest\Filter;

use Common\Filter\NullToFloat;

/**
 * Class NullToFloatTest
 * @package CommonTest\Filter
 * @covers \Common\Filter\NullToFloat
 */
class NullToFloatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getValueDataProvider
     *
     * @param $input
     * @param $expected
     */
    public function testFilter($input, $expected)
    {
        $filter = new NullToFloat();
        $this->assertEquals($expected, $filter->filter($input));
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return [
            'Null value should return int of 0' => [false, 0],
            'Integer value should return same number' => [1, 1],
            'String should return a string' => ['string','string'],
        ];
    }
}
