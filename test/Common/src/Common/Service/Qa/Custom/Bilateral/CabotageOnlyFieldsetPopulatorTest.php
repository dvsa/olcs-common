<?php

namespace CommonTest\Service\Qa\Custom\Bilateral;

use Common\Form\Elements\InputFilters\QaRadio;
use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Bilateral\CabotageOnlyFieldsetPopulator;
use Common\Service\Qa\Custom\Bilateral\YesNoRadioOptionsApplier;
use Common\Service\Qa\RadioFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * CabotageOnlyFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CabotageOnlyFieldsetPopulatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpPopulate
     */
    public function testPopulate($yesNo, $expectedRadioValue)
    {
        $cabotageOnlyNoBlurb = 'Cabotage only no blurb %s';

        $countryName = 'Norway';
        $countryNameTranslated = 'NorwayTranslated';

        $options = [
            'yesNo' => $yesNo,
            'countryName' => $countryName
        ];

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('qanda.bilaterals.cabotage-only.no-blurb')
            ->andReturn($cabotageOnlyNoBlurb);
        $translator->shouldReceive('translate')
            ->with($countryName)
            ->andReturn($countryNameTranslated);

        $yesNoRadio = m::mock(QaRadio::class);
        $yesNoRadio->shouldReceive('setValue')
            ->with($expectedRadioValue)
            ->once();

        $radioFactory = m::mock(RadioFactory::class);
        $radioFactory->shouldReceive('create')
            ->with('qaElement')
            ->andReturn($yesNoRadio);

        $yesNoRadioOptionsApplier = m::mock(YesNoRadioOptionsApplier::class);
        $yesNoRadioOptionsApplier->shouldReceive('applyTo')
            ->with($yesNoRadio)
            ->once();

        $expectedHtmlDefinition = [
            'name' => 'noContent',
            'type' => Html::class,
            'attributes' => [
                'value' => '<div class="govuk-hint">Cabotage only no blurb NorwayTranslated</div>'
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

        $cabotageOnlyFieldsetPopulator = new CabotageOnlyFieldsetPopulator(
            $translator,
            $radioFactory,
            $yesNoRadioOptionsApplier
        );

        $cabotageOnlyFieldsetPopulator->populate($form, $fieldset, $options);
    }

    public function dpPopulate()
    {
        return [
            [null, null],
            ['non_null_string', true],
        ];
    }
}
