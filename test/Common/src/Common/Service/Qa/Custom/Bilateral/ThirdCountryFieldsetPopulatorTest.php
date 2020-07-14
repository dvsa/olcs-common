<?php

namespace CommonTest\Service\Qa\Custom\Bilateral;

use Common\Form\Elements\InputFilters\QaRadio;
use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Bilateral\ThirdCountryFieldsetPopulator;
use Common\Service\Qa\Custom\Bilateral\YesNoRadioOptionsApplier;
use Common\Service\Qa\RadioFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * ThirdCountryFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ThirdCountryFieldsetPopulatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpPopulate
     */
    public function testPopulate($yesNo, $expectedRadioValue)
    {
        $thirdCountryNoBlurb = 'Third country no blurb';

        $options = ['yesNo' => $yesNo];

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('qanda.bilaterals.third-country.no-blurb')
            ->andReturn($thirdCountryNoBlurb);

        $yesNoRadio = m::mock(QaRadio::class);
        $radioFactory = m::mock(RadioFactory::class);
        $radioFactory->shouldReceive('create')
            ->with('qaElement')
            ->andReturn($yesNoRadio);

        $yesNoRadioOptionsApplier = m::mock(YesNoRadioOptionsApplier::class);
        $yesNoRadioOptionsApplier->shouldReceive('applyTo')
            ->with($yesNoRadio, $expectedRadioValue, 'qanda.bilaterals.third-country.not-selected-message')
            ->once();

        $expectedHtmlDefinition = [
            'name' => 'noContent',
            'type' => Html::class,
            'attributes' => [
                'value' => '<div class="govuk-hint">Third country no blurb</div>'
            ]
        ];

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($yesNoRadio)
            ->once()
            ->globally()
            ->ordered();
        $fieldset->shouldReceive('add')
            ->with($expectedHtmlDefinition)
            ->once()
            ->globally()
            ->ordered();
        $fieldset->shouldReceive('setOption')
            ->with('radio-element', 'qaElement')
            ->once();

        $form = m::mock(Form::class);

        $thirdCountryFieldsetPopulator = new ThirdCountryFieldsetPopulator(
            $translator,
            $radioFactory,
            $yesNoRadioOptionsApplier
        );

        $thirdCountryFieldsetPopulator->populate($form, $fieldset, $options);
    }

    public function dpPopulate()
    {
        return [
            [null, null],
            ['non_null_string', true],
        ];
    }
}
