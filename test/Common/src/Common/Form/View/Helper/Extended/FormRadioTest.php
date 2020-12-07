<?php

namespace CommonTest\Form\View\Helper\Extended;

use Common\Form\View\Helper\FormRow;
use Common\View\Helper\UniqidGenerator;
use CommonTest\Form\View\Helper\Extended\Stub\FormRadioChildContentStub;
use CommonTest\Form\View\Helper\Extended\Stub\FormRadioStub;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\Element\MultiCheckbox;
use Mockery as m;
use Laminas\Form\View\Helper\FormCollection;
use Laminas\I18n\Translator\TranslatorInterface;

class FormRadioTest extends MockeryTestCase
{
    /**
     * @dataProvider renderOptionsProvider
     */
    public function testRenderOptions($options, $selectedOptions, $attributes, $globalAttributes, $labelPosition, $expected)
    {
        $_SERVER['REQUEST_URI'] = '/test/uri';
        $idGenerator = null;
        if (!empty($options) && !isset($attributes['id'])) {
            $idGenerator = m::mock(UniqidGenerator::class);
            $idGenerator->shouldReceive('generateId')->once()->andReturn('generated_id');
        }
        $sut = new FormRadioStub($idGenerator);
        $translator = m::mock(TranslatorInterface::class);
        $translator->shouldReceive('translate')->andReturnUsing(function ($string, $domain) {
            return $domain.'-translated-'.$string;
        });
        $sut->setTranslator($translator);
        if (!is_null($globalAttributes)) {
            $sut->setLabelAttributes($globalAttributes);
        }
        if (!is_null($labelPosition)) {
            $sut->setLabelPosition($labelPosition);
        }
        $renderer = m::mock(\Laminas\View\Renderer\RendererInterface::class);
        $formCollection = m::mock(FormCollection::class);
        $formCollection->shouldReceive('setReadOnly');
        $renderer->shouldReceive('formCollection')->andReturn($formCollection);
        $formRow = m::mock(FormRow::class);
        $formRow->shouldReceive('__invoke')->andReturn('child_row');
        $renderer->shouldReceive('plugin')->andReturn($formRow);
        $sut->setView($renderer);

        $element = new MultiCheckbox();

        $output = $sut->renderOptions($element, $options, $selectedOptions, $attributes);
        $this->assertSame($expected, html_entity_decode($output));
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
                            'id' => 'aaa_id',
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
                            'id' => 'bbb_id',
                        ],
                        'hint_attributes' => [
                            'class' => 'hint_class',
                        ],
                        'markup_before' => '<div>markup before</div>'
                    ],
                ],
                'selectedOptions' => [],
                'attributes' => [
                    'id' => 'input_id',
                    'class' => 'input class',
                    'radios_wrapper_attributes' => [
                        'class' => 'radios_wrapper_class',
                        'data-something' => "some_data"
                    ],
                ],
                'globalAttributes' => null,
                'labelPosition' => FormRadioStub::LABEL_PREPEND,
                'expected' => '<div class="govuk-radios radios_wrapper_class" data-something="some_data"><div class="govuk-radios__item"><label class="label_class govuk-label govuk-radios__label" for="aaa_id">default-translated-aaa</label><input id="aaa_id" class="input_class govuk-radios__input" value="A"></div><div>markup before</div><div class="govuk-radios__item"><label class="govuk-label govuk-radios__label" for="bbb_id">default-translated-bbb</label><input class="govuk-radios__input" id="bbb_id" value="B"></div></div>'
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
                    ],
                ],
                'selectedOptions' => ['B'],
                'attributes' => [
                    'class' => 'input class'
                ],
                'globalAttributes' => [],
                'labelPosition' => null,
                'expected' => '<div class="govuk-radios"><div class="govuk-radios__item"><input class="input_class govuk-radios__input" value="B" checked="checked" id="generated_id"><label class="label_class govuk-label govuk-radios__label" for="generated_id">default-translated-bbb</label><div class="hint_class govuk-hint govuk-radios__hint">default-translated-hint_text</div></div></div>'
            ],
        ];
    }
}
