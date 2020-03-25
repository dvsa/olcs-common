<?php

namespace CommonTest\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\Custom\Bilateral\YesNoRadioOptionsApplier;
use Common\Service\Qa\Custom\Bilateral\Radio;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * YesNoRadioOptionsApplierTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class YesNoRadioOptionsApplierTest extends MockeryTestCase
{
    public function testApplyTo()
    {
        $standardAttributes = [
            'radios_wrapper_attributes' => [
                'class' => 'govuk-radios--conditional',
                'data-module' => 'radios',
            ]
        ];

        $standardValueOptions = [
            'yes' => [
                'label' => 'Yes',
                'value' => 'Y',
            ],
            'no' => [
                'label' => 'No',
                'value' => 'N',
            ]
        ];

        $radio = m::mock(Radio::class);

        $radio->shouldReceive('setValueOptions')
            ->with($standardValueOptions)
            ->once();

        $radio->shouldReceive('setAttributes')
            ->with($standardAttributes)
            ->once();

        $radio->shouldReceive('setOption')
            ->with('not_selected_message', 'qanda.bilaterals.cabotage.not-selected-message')
            ->once();

        $yesNoRadioOptionsApplier = new YesNoRadioOptionsApplier();
        $yesNoRadioOptionsApplier->applyTo($radio);
    }
}
