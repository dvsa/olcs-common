<?php

namespace CommonTest\Service\Qa\Custom\Bilateral;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Bilateral\PermitUsageFieldsetPopulator;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\RadioFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Submit;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * PermitUsageFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitUsageFieldsetPopulatorTest extends MockeryTestCase
{
    private $form;

    private $fieldset;

    private $radioFieldsetPopulator;

    private $translator;

    private $htmlAdder;

    private $permitUsageFieldsetPopulator;

    public function setUp(): void
    {
        $this->form = m::mock(Form::class);

        $warningElementAttributes = [
            'name' => 'warningVisible',
            'type' => Hidden::class,
            'attributes' => [
                'value' => 0
            ]
        ];

        $this->fieldset = m::mock(Fieldset::class);
        $this->fieldset->shouldReceive('add')
            ->with($warningElementAttributes)
            ->once();

        $this->radioFieldsetPopulator = m::mock(RadioFieldsetPopulator::class);

        $this->translator = m::mock(TranslationHelperService::class);

        $this->htmlAdder = m::mock(HtmlAdder::class);

        $this->permitUsageFieldsetPopulator = new PermitUsageFieldsetPopulator(
            $this->radioFieldsetPopulator,
            $this->translator,
            $this->htmlAdder
        );
    }

    /**
     * @dataProvider dpPopulateSingleItem
     */
    public function testPopulateSingleItem($firstOptionValue, $expectedTranslationKey)
    {
        $options = [
            'options' => [
                [
                    'value' => $firstOptionValue
                ]
            ]
        ];

        $this->translator->shouldReceive('translate')
            ->with($expectedTranslationKey)
            ->andReturn('Translated option caption');

        $this->htmlAdder->shouldReceive('add')
            ->with($this->fieldset, 'qaHtml', '<p class="govuk-body-l">Translated option caption</p>')
            ->once();

        $expectedHiddenParams = [
            'name' => 'qaElement',
            'type' => Hidden::class,
            'attributes' => [
                'value' => $firstOptionValue,
            ]
        ];

        $submitButton = m::mock(Submit::class);
        $submitButton->shouldReceive('setValue')
            ->with('permits.button.continue')
            ->once();

        $submitFieldset = m::mock(Fieldset::class);
        $submitFieldset->shouldReceive('get')
            ->with('SubmitButton')
            ->andReturn($submitButton);

        $this->form->shouldReceive('get')
            ->with('Submit')
            ->andReturn($submitFieldset);

        $this->fieldset->shouldReceive('add')
            ->with($expectedHiddenParams)
            ->once();

        $this->permitUsageFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }

    public function dpPopulateSingleItem()
    {
        return [
            [
                'journey_single',
                'qanda.bilaterals.permit-usage.single-option.journey-single'
            ],
            [
                'journey_multiple',
                'qanda.bilaterals.permit-usage.single-option.journey-multiple'
            ],
        ];
    }

    public function testPopulateTwoItems()
    {
        $firstOptionValue = 'journey_multiple';
        $secondOptionValue = 'journey_single';

        $options = [
            'options' => [
                [
                    'value' => $firstOptionValue
                ],
                [
                    'value' => $secondOptionValue
                ]
            ],
            'otherKey1' => 'otherKey1Value',
            'otherKey2' => 'otherKey2Value',
        ];

        $expectedUpdatedOptions = [
            'options' => [
                [
                    'value' => $firstOptionValue,
                    'label' => 'qanda.bilaterals.permit-usage.multiple-options.journey-multiple'
                ],
                [
                    'value' => $secondOptionValue,
                    'label' => 'qanda.bilaterals.permit-usage.multiple-options.journey-single'
                ]
            ],
            'otherKey1' => 'otherKey1Value',
            'otherKey2' => 'otherKey2Value',
        ];

        $this->radioFieldsetPopulator->shouldReceive('populate')
            ->with($this->form, $this->fieldset, $expectedUpdatedOptions)
            ->once();

        $this->permitUsageFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }
}
