<?php


namespace CommonTest\Form\View\Helper\Readonly;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Form\View\Helper\Readonly\FormSelect;
use Mockery as m;

/**
 * Class FormSelectTest
 * @package CommonTest\Form\View\Helper\Readonly
 */
class FormSelectTest extends TestCase
{
    /**
     * @param $element
     * @param $expected
     * @dataProvider provideTestInvoke
     */
    public function testInvoke($element, $expected)
    {
        $sut =  new FormSelect();
        $expected = (($expected !== null) ? $expected : $sut);
        $this->assertEquals($expected, $sut($element));
    }

    public function provideTestInvoke()
    {
        $valueOptions = [
            'group1' => [
                'options' => [
                    ['value' => 'val1', 'label' => 'Val 1'],
                    'val3' => 'Val 3'
                ]
            ],
            'val2' => 'Val 2'
        ];

        $mockMultiple = m::mock('Zend\Form\Element\Select');
        $mockMultiple->shouldReceive('getAttribute')->with('multiple')->andReturn(true);
        $mockMultiple->shouldReceive('getValueOptions')->andReturn($valueOptions);
        $mockMultiple->shouldReceive('getValue')->andReturn(['val1', 'val2']);

        $mockSingle = m::mock('Zend\Form\Element\Select');
        $mockSingle->shouldReceive('getAttribute')->with('multiple')->andReturn(false);
        $mockSingle->shouldReceive('getValueOptions')->andReturn($valueOptions);
        $mockSingle->shouldReceive('getValue')->andReturn('val3');

        return [
            [$mockMultiple, 'Val 1, Val 2'],
            [$mockSingle, 'Val 3'],
            [m::mock('Zend\Form\ElementInterface'), ''],
            [null, null]
        ];
    }
}
