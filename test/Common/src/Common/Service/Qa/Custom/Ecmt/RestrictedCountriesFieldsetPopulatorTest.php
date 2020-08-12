<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\Custom\Ecmt\RestrictedCountriesFieldsetPopulator;
use Common\Service\Qa\Custom\Ecmt\YesNoRadio;
use Common\Service\Qa\Custom\Ecmt\YesNoRadioFactory;
use Common\Service\Qa\Custom\Ecmt\RestrictedCountriesMultiCheckbox;
use Common\Service\Qa\Custom\Ecmt\RestrictedCountriesMultiCheckboxFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * RestrictedCountriesFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountriesFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $questionKey = 'question.key';
 
        $options = [
            'yesNo' => null,
            'questionKey' => $questionKey,
            'countries' => [
                [
                    'code' => 'GR',
                    'labelTranslationKey' => 'Greece',
                    'checked' => true
                ],
                [
                    'code' => 'HU',
                    'labelTranslationKey' => 'Hungary',
                    'checked' => false
                ],
                [
                    'code' => 'IT',
                    'labelTranslationKey' => 'Italy',
                    'checked' => true
                ],
            ]
        ];

        $expectedValueOptions = [
            [
                'value' => 'GR',
                'label' => 'Greece',
                'selected' => true,
                'attributes' => [
                    'id' => 'RestrictedCountriesList',
                ],
            ],
            [
                'value' => 'HU',
                'label' => 'Hungary',
                'selected' => false
            ],
            [
                'value' => 'IT',
                'label' => 'Italy',
                'selected' => true
            ],
        ];


        $fieldsetName = 'fieldset12';
        $yesNoRadioName = 'restrictedCountries';
        $multiCheckboxName = 'yesContent';

        $form = m::mock(Form::class);

        $yesNoRadio = m::mock(YesNoRadio::class);
        $yesNoRadio->shouldReceive('setStandardValueOptions')
            ->withNoArgs()
            ->once();
        $yesNoRadio->shouldReceive('setValue')
            ->with(null)
            ->once();

        $restrictedCountriesMultiCheckbox = m::mock(RestrictedCountriesMultiCheckbox::class);
        $restrictedCountriesMultiCheckbox->shouldReceive('setValueOptions')
            ->with($expectedValueOptions)
            ->once();

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('getName')
            ->andReturn($fieldsetName);
        $fieldset->shouldReceive('setOption')
            ->with('radio-element', 'restrictedCountries')
            ->once();
        $fieldset->shouldReceive('add')
            ->with($yesNoRadio)
            ->once()
            ->ordered();
        $fieldset->shouldReceive('add')
            ->with($restrictedCountriesMultiCheckbox)
            ->once()
            ->ordered();

        $fieldset->shouldReceive('setLabel')
            ->with($questionKey)
            ->once();
        $fieldset->shouldReceive('setLabelAttributes')
            ->with(['class' => 'govuk-visually-hidden'])
            ->once();

        $yesNoRadioFactory = m::mock(YesNoRadioFactory::class);
        $yesNoRadioFactory->shouldReceive('create')
            ->with($yesNoRadioName)
            ->once()
            ->andReturn($yesNoRadio);

        $restrictedCountriesMultiCheckboxFactory = m::mock(RestrictedCountriesMultiCheckboxFactory::class);
        $restrictedCountriesMultiCheckboxFactory->shouldReceive('create')
            ->with($multiCheckboxName)
            ->once()
            ->andReturn($restrictedCountriesMultiCheckbox);

        $yesNoRadio->shouldReceive('setOption')
            ->with('yesContentElement', $restrictedCountriesMultiCheckbox)
            ->once();

        $restrictedCountriesFieldsetPopulator = new RestrictedCountriesFieldsetPopulator(
            $yesNoRadioFactory,
            $restrictedCountriesMultiCheckboxFactory
        );

        $restrictedCountriesFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
