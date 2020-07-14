<?php

namespace CommonTest\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\Custom\Bilateral\Radio;
use Common\Service\Qa\Custom\Bilateral\RadioFactory;
use Common\Service\Qa\Custom\Bilateral\StandardAndCabotageFieldsetPopulator;
use Common\Service\Qa\Custom\Bilateral\StandardAndCabotageYesNoRadio;
use Common\Service\Qa\Custom\Bilateral\StandardAndCabotageYesNoRadioFactory;
use Common\Service\Qa\Custom\Bilateral\YesNoRadioOptionsApplier;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * StandardAndCabotageFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StandardAndCabotageFieldsetPopulatorTest extends MockeryTestCase
{
    private $yesContentRadio;

    private $radioFactory;

    private $yesNoRadio;

    private $standardAndCabotageYesNoRadioFactory;

    private $yesNoRadioOptionsApplier;

    private $form;

    private $fieldset;

    private $standardAndCabotageFieldsetPopulator;

    public function setUp(): void
    {
        $expectedValueOptions = [
            StandardAndCabotageFieldsetPopulator::ANSWER_CABOTAGE_ONLY
                => StandardAndCabotageFieldsetPopulator::ANSWER_CABOTAGE_ONLY,
                StandardAndCabotageFieldsetPopulator::ANSWER_STANDARD_AND_CABOTAGE
                    => StandardAndCabotageFieldsetPopulator::ANSWER_STANDARD_AND_CABOTAGE
        ];

        $this->yesContentRadio = m::mock(Radio::class);
        $this->yesContentRadio->shouldReceive('setValueOptions')
            ->with($expectedValueOptions)
            ->once();

        $this->radioFactory = m::mock(RadioFactory::class);
        $this->radioFactory->shouldReceive('create')
            ->with('yesContent')
            ->once()
            ->andReturn($this->yesContentRadio);

        $this->yesNoRadio = m::mock(StandardAndCabotageYesNoRadio::class);
        $this->yesNoRadio->shouldReceive('setOption')
            ->with('yesContentElement', $this->yesContentRadio)
            ->once();

        $this->standardAndCabotageYesNoRadioFactory = m::mock(StandardAndCabotageYesNoRadioFactory::class);
        $this->standardAndCabotageYesNoRadioFactory->shouldReceive('create')
            ->with('qaElement')
            ->once()
            ->andReturn($this->yesNoRadio);

        $this->yesNoRadioOptionsApplier = m::mock(YesNoRadioOptionsApplier::class);

        $this->form = m::mock(Form::class);

        $this->fieldset = m::mock(Fieldset::class);
        $this->fieldset->shouldReceive('add')
            ->with($this->yesNoRadio)
            ->once()
            ->globally()
            ->ordered();
        $this->fieldset->shouldReceive('add')
            ->with($this->yesContentRadio)
            ->once()
            ->globally()
            ->ordered();
        $this->fieldset->shouldReceive('setOption')
            ->with('radio-element', 'qaElement')
            ->once();

        $this->standardAndCabotageFieldsetPopulator = new StandardAndCabotageFieldsetPopulator(
            $this->radioFactory,
            $this->standardAndCabotageYesNoRadioFactory,
            $this->yesNoRadioOptionsApplier
        );
    }

    public function testPopulateNull()
    {
        $options = [
            'value' => null
        ];

        $this->yesNoRadioOptionsApplier->shouldReceive('applyTo')
            ->with($this->yesNoRadio, null, 'qanda.bilaterals.cabotage.not-selected-message')
            ->once();

        $this->standardAndCabotageFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }

    public function testPopulateCabotageNotRequired()
    {
        $options = [
            'value' => StandardAndCabotageFieldsetPopulator::ANSWER_STANDARD_ONLY
        ];

        $this->yesNoRadioOptionsApplier->shouldReceive('applyTo')
            ->with($this->yesNoRadio, 'N', 'qanda.bilaterals.cabotage.not-selected-message')
            ->once();

        $this->standardAndCabotageFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }

    /**
     * @dataProvider dpPopulateCabotageRequired
     */
    public function testPopulateCabotageRequired($answerValue)
    {
        $options = [
            'value' => $answerValue
        ];

        $this->yesNoRadioOptionsApplier->shouldReceive('applyTo')
            ->with($this->yesNoRadio, 'Y', 'qanda.bilaterals.cabotage.not-selected-message')
            ->once();

        $this->yesContentRadio->shouldReceive('setValue')
            ->with($answerValue)
            ->once();

        $this->standardAndCabotageFieldsetPopulator->populate($this->form, $this->fieldset, $options);
    }

    public function dpPopulateCabotageRequired()
    {
        return [
            [StandardAndCabotageFieldsetPopulator::ANSWER_CABOTAGE_ONLY],
            [StandardAndCabotageFieldsetPopulator::ANSWER_STANDARD_AND_CABOTAGE],
        ];
    }
}
