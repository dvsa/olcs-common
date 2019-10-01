<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\Custom\EcmtShortTerm\RestrictedCountriesFieldsetPopulator;
use Common\Service\Qa\Custom\EcmtShortTerm\YesNoRadio;
use Common\Service\Qa\Custom\EcmtShortTerm\YesNoRadioFactory;
use Common\Service\Qa\Custom\EcmtShortTerm\RestrictedCountriesMultiCheckboxFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\MultiCheckbox;
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
        $options = [
            'yesNo' => null,
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
                'selected' => true
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
        $yesNoRadio->shouldReceive('setOption')
            ->with('form', $form)
            ->once();
        $yesNoRadio->shouldReceive('setOption')
            ->with('fieldsetName', $fieldsetName)
            ->once();
        $yesNoRadio->shouldReceive('setValue')
            ->with(null)
            ->once();

        $multiCheckbox = m::mock(MultiCheckbox::class);
        $multiCheckbox->shouldReceive('setValueOptions')
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
            ->with($multiCheckbox)
            ->once()
            ->ordered();

        $yesNoRadioFactory = m::mock(YesNoRadioFactory::class);
        $yesNoRadioFactory->shouldReceive('create')
            ->with($yesNoRadioName)
            ->once()
            ->andReturn($yesNoRadio);

        $restrictedCountriesMultiCheckboxFactory = m::mock(RestrictedCountriesMultiCheckboxFactory::class);
        $restrictedCountriesMultiCheckboxFactory->shouldReceive('create')
            ->with($multiCheckboxName)
            ->once()
            ->andReturn($multiCheckbox);

        $restrictedCountriesFieldsetPopulator = new RestrictedCountriesFieldsetPopulator(
            $yesNoRadioFactory,
            $restrictedCountriesMultiCheckboxFactory
        );

        $restrictedCountriesFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
