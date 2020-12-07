<?php

namespace CommonTest\Form\View\Helper\Readonly;

use Common\Form\View\Helper\Readonly\FormDateSelect;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class FormdateSelectTest
 * @package CommonTest\Form\View\Helper\Readonly
 */
class FormDateSelectTest extends TestCase
{
    /**
     * @param $element
     * @param $expected
     * @dataProvider provideTestInvoke
     */
    public function testInvoke($element, $expected)
    {
        $sut = new FormDateSelect();
        $expected = (($expected !== null) ? $expected : $sut);
        $this->assertEquals($expected, $sut($element));
    }

    /**
     * @return array
     */
    public function provideTestInvoke()
    {
        $mockDs = m::mock('Laminas\Form\Element\DateSelect');
        $mockDs->shouldReceive('getYearElement->getValue')->andReturn('2014');
        $mockDs->shouldReceive('getMonthElement->getValue')->andReturn('11');
        $mockDs->shouldReceive('getDayElement->getValue')->andReturn('28');

        $mockDsEmpty = m::mock('Laminas\Form\Element\DateSelect');
        $mockDsEmpty->shouldReceive('getYearElement->getValue')->andReturn(null);

        return [
            [$mockDs, '28/11/2014'],
            [$mockDsEmpty, ''],
            [null, null],
            [m::mock('Laminas\Form\ElementInterface'), '']
        ];
    }
}
