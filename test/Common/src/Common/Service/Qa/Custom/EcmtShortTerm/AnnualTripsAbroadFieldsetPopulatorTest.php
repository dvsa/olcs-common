<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\EcmtShortTerm\AnnualTripsAbroadFieldsetPopulator;
use Common\Service\Qa\TextFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * AnnualTripsAbroadFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnnualTripsAbroadFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $markup = '<p>paragraph 1</p><p>paragraph</p>';

        $options = [
            'key1' => 'value1',
            'key2' => 'value2'
        ];

        $expectedAnnotationDefinition = [
            'name' => 'hint',
            'type' => Html::class,
            'attributes' => [
                'value' => $markup
            ]
        ];

        $form = m::mock(Form::class);

        $fieldset = m::mock(Fieldset::class);

        $fieldset->shouldReceive('add')
            ->with($expectedAnnotationDefinition)
            ->once()
            ->globally()
            ->ordered();

        $textFieldsetPopulator = m::mock(TextFieldsetPopulator::class);
        $textFieldsetPopulator->shouldReceive('populate')
            ->with($form, $fieldset, $options)
            ->once()
            ->globally()
            ->ordered();

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('markup-ecmt-trips-hint')
            ->andReturn($markup);

        $annualTripsAbroadFieldsetPopulator = new AnnualTripsAbroadFieldsetPopulator(
            $textFieldsetPopulator,
            $translator
        );

        $annualTripsAbroadFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
