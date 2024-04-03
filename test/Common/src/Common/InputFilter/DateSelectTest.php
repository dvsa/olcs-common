<?php

namespace CommonTest\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * DateSelectTest
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DateSelectTest extends TestCase
{
    /**
     * @dataProvider dataProviderTestGetValue
     */
    public function testGetRawValue($value, $expected): void
    {
        $sut = new \Common\InputFilter\DateSelect();
        $sut->setValue($value);

        $this->assertSame($expected, $sut->getRawValue());
    }

    public function dataProviderTestGetValue()
    {
        return [
            // value, expected
            ['foo', 'foo'],
            [null, null],
            [['month' => 2, 'year' => 3], ['month' => 2, 'year' => 3]],
            [['day' => 1, 'month' => 2, 'year' => 3], ['day' => 1, 'month' => 2, 'year' => 3]],
            [['day' => '', 'month' => '', 'year' => ''], null],
        ];
    }
}
