<?php


namespace CommonTest\Form\View\Helper\Readonly;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Form\View\Helper\Readonly\FormTable;
use Mockery as m;

/**
 * Class FormTableTest
 * @package CommonTest\Form\View\Helper\Readonly
 */
class FormTableTest extends TestCase
{
    /**
     * @dataProvider provideTestInvoke
     * @param $element
     * @param $expected
     */
    public function testInvoke($element, $expected)
    {

        $mockView = new \Zend\View\Renderer\PhpRenderer();

        $sut = new FormTable();

        $sut->setView($mockView);

        $expected = (($expected !== null) ? $expected : $sut);
        $this->assertEquals($expected, $sut($element));
    }

    public function provideTestInvoke()
    {
        //need tests for Select, TextArea

        $mockHidden = m::mock('Zend\Form\ElementInterface');
        $mockHidden->shouldReceive('getAttribute')->with('type')->andReturn('hidden');

        $mockRemoveIfReadOnly = m::mock('Zend\Form\ElementInterface');
        $mockRemoveIfReadOnly->shouldReceive('getAttribute')->with('type')->andReturnNull();
        $mockRemoveIfReadOnly->shouldReceive('getOption')->with('remove_if_readonly')->andReturn(true);

        $mockText = m::mock('Zend\Form\ElementInterface');
        $mockText->shouldReceive('getAttribute')->with('type')->andReturn('textarea');
        $mockText->shouldReceive('getLabel')->andReturn('Label');
        $mockText->shouldReceive('getValue')->andReturn('Value');
        $mockText->shouldReceive('getOption')->with('remove_if_readonly')->andReturnNull();

        $mockSelect = m::mock('Zend\Form\Element\Select');
        $mockSelect->shouldReceive('getAttribute')->with('type')->andReturn('select');
        $mockSelect->shouldReceive('getLabel')->andReturn('Label');
        $mockSelect->shouldReceive('getValue')->andReturn('Value');
        $mockSelect->shouldReceive('getLabelOption')->andReturn(false);
        $mockSelect->shouldReceive('getAttribute')->andReturn(false);
        $mockSelect->shouldReceive('getOption')->with('remove_if_readonly')->andReturnNull();

        $columns = [
            0 => [
                'name' => 'foo',
            ],
            1 => [
                'name' => 'checkbox',
                'type' => 'Checkbox'
            ]
        ];
        $newColumns = [
            0 => [
                'name' => 'foo',
            ]
        ];

        $mockTableBuilder = m::mock('Common\Service\Table\TableBuilder');
        $mockTableBuilder->shouldReceive('setDisabled')->with(true);
        $mockTableBuilder->shouldReceive('getColumns')->andReturn($columns);
        $mockTableBuilder->shouldReceive('setColumns')->with($newColumns);

        $mockTable = m::mock('Common\Form\Elements\Types\Table');
        $mockTable->shouldReceive('getTable')->andReturn($mockTableBuilder);
        $mockTable->shouldReceive('render')->andReturn('<table></table>');

        return [
            [null, null],
            [$mockHidden, ''],
            [$mockRemoveIfReadOnly, ''],
            [$mockText, ''],
            [$mockSelect, ''],
            [$mockTable, '<table></table>'],
        ];
    }
}
