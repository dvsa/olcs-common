<?php

namespace CommonTest\Form\View\Helper\Extended;

use Common\Form\View\Helper\FormRow;
use CommonTest\Form\View\Helper\Extended\Stub\FormRadioChildContentStub;
use CommonTest\Form\View\Helper\Extended\Stub\FormRadioStub;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\MultiCheckbox;
use Mockery as m;
use Zend\Form\View\Helper\FormCollection;
use Zend\I18n\Translator\TranslatorInterface;

class FormRadioTest extends MockeryTestCase
{
    /**
     * @dataProvider renderOptionsProvider
     */
    public function testRenderOptions($options, $selectedOptions, $attributes, $globalAttributes, $labelPosition, $expected)
    {
        $_SERVER['REQUEST_URI'] = '/test/uri';
        $sut = new FormRadioStub();
        $translator = m::mock(TranslatorInterface::class);
        $translator->shouldReceive('translate')->andReturnUsing(function ($string, $domain) {
            return $string;
        });
        $sut->setTranslator($translator);
        if (!is_null($globalAttributes)) {
            $sut->setLabelAttributes($globalAttributes);
        }
        if (!is_null($labelPosition)) {
            $sut->setLabelPosition($labelPosition);
        }
        $renderer = m::mock(\Zend\View\Renderer\RendererInterface::class);
        $formCollection = m::mock(FormCollection::class);
        $formCollection->shouldReceive('setReadOnly');
        $renderer->shouldReceive('formCollection')->andReturn($formCollection);
        $formRow = m::mock(FormRow::class);
        $formRow->shouldReceive('__invoke')->andReturn('child_row');
        $renderer->shouldReceive('plugin')->andReturn($formRow);
        $sut->setView($renderer);

        $element = new MultiCheckbox();

        $output = $sut->renderOptions($element, $options, $selectedOptions, $attributes);
        $this->assertSame($expected, $output);
    }

    public function renderOptionsProvider()
    {
        return [
            'nothing_to_render' => [
                'options' => [],
                'selectedOptions' => [],
                'attributes' => [],
                'globalAttributes' => null,
                'labelPosition' => null,
                'expected' => ''
            ],
            'options_set_1' => [
                'options' => [
                    'A' => [
                        'label' => 'aaa',
                        'value' => 'A',
                        'wrapper_attributes' => [
                            'class' => 'wrapper_class',
                        ],
                        'attributes' => [
                            'class' => 'input_class',
                        ],
                        'label_attributes' => [
                            'class' => 'label_class',
                        ],
                        'hint_attributes' => [
                            'class' => 'hint_class',
                        ],
                    ],
                    'B' => [
                        'label' => 'bbb',
                        'value' => 'B',
                        'wrapper_attributes' => [
                            'class' => 'wrapper_class',
                        ],
                        'attributes' => [
                            'class' => 'input_class',
                        ],
                        'label_attributes' => [
                            'class' => 'label_class',
                        ],
                        'hint_attributes' => [
                            'class' => 'hint_class',
                        ],
                    ],
                ],
                'selectedOptions' => [],
                'attributes' => [
                    'id' => 'input_id',
                    'class' => 'input class'
                ],
                'globalAttributes' => null,
                'labelPosition' => FormRadioStub::LABEL_PREPEND,
                'expected' => '<div class="wrapper_class"><label class="label_class">aaa</label><input id="input_id" class="input_class" value="A"></div><div class="wrapper_class"><label class="label_class">bbb</label><input class="input_class" value="B"></div>'
            ],
            'options_set_2' => [
                'options' => [
                    'B' => [
                        'label' => 'bbb',
                        'value' => 'B',
                        'hint' => 'hint_text',
                        'selected' => true,
                        'disabled' => false,
                        'wrapper_attributes' => [
                            'class' => 'wrapper_class',
                        ],
                        'attributes' => [
                            'class' => 'input_class',
                        ],
                        'label_attributes' => [
                            'class' => 'label_class',
                        ],
                        'hint_attributes' => [
                            'class' => 'hint_class',
                        ],
                        'childContent' => [
                            'content' => FormRadioChildContentStub::class,
                            'attributes' => [
                                'id' => 'child_id',
                                'class' => 'child_class',
                            ]
                        ]
                    ],
                ],
                'selectedOptions' => ['B'],
                'attributes' => [
                    'id' => 'input_id',
                    'class' => 'input class'
                ],
                'globalAttributes' => [],
                'labelPosition' => null,
                'expected' => '<div class="wrapper_class"><input id="input_id" class="input_class" value="B" checked="checked"><label class="label_class">bbb</label><div class="hint_class">hint_text</div></div><div id="child_id" class="child_class">child_row</div>'
            ],
        ];
    }
}
