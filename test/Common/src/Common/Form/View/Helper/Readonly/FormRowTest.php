<?php


namespace CommonTest\Form\View\Helper\Readonly;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Form\View\Helper\Readonly\FormRow;
use Mockery as m;

/**
 * Class FormRowTest
 * @package CommonTest\Form\View\Helper\Readonly
 */
class FormRowTest extends TestCase
{
    /**
     * @dataProvider provideTestInvoke
     * @param $element
     * @param $expected
     */
    public function testInvoke($element, $expected)
    {
        $callback = function ($v) {
            return $v;
        };

        $mockHtmlHelper = m::mock('Zend\View\Helper\EscapeHtml');
        $mockHtmlHelper->shouldReceive('__invoke')->andReturnUsing($callback);
        $mockElementHelper = m::mock('Common\Form\View\Helper\Readonly\FormItem');
        $mockElementHelper->shouldReceive('__invoke')->andReturnUsing(
            function ($v) {
                return $v->getValue();
            }
        );
        $mockTableHelper = m::mock('Common\Form\View\Helper\Readonly\FormTable');
        $mockTableHelper->shouldReceive('__invoke')->andReturn('<table></table>');

        $mockTranslater = m::mock('Zend\I18n\Translator\TranslatorInterface');
        $mockTranslater->shouldReceive('translate')->andReturnUsing($callback);

        $mockView = m::mock('Zend\View\Renderer\PhpRenderer');
        $mockView->shouldReceive('plugin')->with('escapehtml')->andReturn($mockHtmlHelper);
        $mockView->shouldReceive('plugin')->with('readonlyformitem')->andReturn($mockElementHelper);
        $mockView->shouldReceive('plugin')->with('readonlyformselect')->andReturn($mockElementHelper);
        $mockView->shouldReceive('plugin')->with('readonlyformtable')->andReturn($mockTableHelper);

        $sut = new FormRow();

        $sut->setView($mockView);
        $sut->setTranslator($mockTranslater);

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
        $mockTable->shouldReceive('getAttribute')->with('type')->andReturnNull();
        $mockTable->shouldReceive('getOption')->with('remove_if_readonly')->andReturnNull();

        $mockTable->shouldReceive('getTable')->andReturn($mockTableBuilder);
        $mockTable->shouldReceive('render')->andReturn('<table></table>');

        return [
            [null, null],
            [$mockHidden, ''],
            [$mockRemoveIfReadOnly, ''],
            [$mockText, '<li class="definition-list__item full-width"><dt>Label</dt><dd>Value</dd></li>'],
            [$mockSelect, '<li class="definition-list__item"><dt>Label</dt><dd>Value</dd></li>'],
            [$mockTable, '<table></table>']

        ];
    }
}
