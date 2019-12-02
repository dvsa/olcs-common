<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\EcmtShortTerm\AnnualTripsAbroadFieldsetPopulator;
use Common\Service\Qa\Custom\EcmtShortTerm\NiWarningConditionalAdder;
use Common\Service\Qa\TextFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Hidden;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * AnnualTripsAbroadFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnnualTripsAbroadFieldsetPopulatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTrueFalse
     */
    public function testPopulate($showNiWarning)
    {
        $markup = '<div class="guidance-blue">Trips abroad guidance</div><p>paragraph 1</p><p>paragraph</p>';

        $textOptions = [
            'textKey1' => 'textValue1',
            'textKey2' => 'textValue2'
        ];

        $options = [
            'showNiWarning' => $showNiWarning,
            'text' => $textOptions
        ];

        $expectedAnnotationDefinition = [
            'name' => 'hint',
            'type' => Html::class,
            'attributes' => [
                'value' => $markup
            ]
        ];

        $expectedWarningVisibleParameters = [
            'name' => 'warningVisible',
            'type' => Hidden::class,
            'attributes' => [
                'value' => 0
            ]
        ];

        $form = m::mock(Form::class);

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($expectedWarningVisibleParameters)
            ->once();

        $niWarningConditionalAdder = m::mock(NiWarningConditionalAdder::class);
        $niWarningConditionalAdder->shouldReceive('addIfRequired')
            ->with($fieldset, $showNiWarning)
            ->once()
            ->globally()
            ->ordered();

        $fieldset->shouldReceive('add')
            ->with($expectedAnnotationDefinition)
            ->once()
            ->globally()
            ->ordered();

        $textFieldsetPopulator = m::mock(TextFieldsetPopulator::class);
        $textFieldsetPopulator->shouldReceive('populate')
            ->with($form, $fieldset, $textOptions)
            ->once()
            ->globally()
            ->ordered();

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('markup-ecmt-trips-hint')
            ->andReturn('<p>paragraph 1</p><p>paragraph</p>');
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt-short-term.annual-trips-abroad.guidance')
            ->andReturn('Trips abroad guidance');

        $annualTripsAbroadFieldsetPopulator = new AnnualTripsAbroadFieldsetPopulator(
            $textFieldsetPopulator,
            $translator,
            $niWarningConditionalAdder
        );

        $annualTripsAbroadFieldsetPopulator->populate($form, $fieldset, $options);
    }

    public function dpTrueFalse()
    {
        return [
            [true],
            [false]
        ];
    }
}