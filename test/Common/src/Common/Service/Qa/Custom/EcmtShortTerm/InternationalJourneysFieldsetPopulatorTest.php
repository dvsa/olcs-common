<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\Custom\EcmtShortTerm\InternationalJourneysFieldsetPopulator;
use Common\Service\Qa\Custom\EcmtShortTerm\NiWarningConditionalAdder;
use Common\Service\Qa\RadioFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Hidden;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * InternationalJourneysFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class InternationalJourneysFieldsetPopulatorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTrueFalse
     */
    public function testPopulate($showNiWarning)
    {
        $radioOptions = [
            'radioKey1' => 'radioValue1',
            'radioKey2' => 'radioValue2'
        ];

        $options = [
            'showNiWarning' => $showNiWarning,
            'radio' => $radioOptions
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

        $radioFieldsetPopulator = m::mock(RadioFieldsetPopulator::class);
        $radioFieldsetPopulator->shouldReceive('populate')
            ->with($form, $fieldset, $radioOptions)
            ->once()
            ->globally()
            ->ordered();

        $internationalJourneysFieldsetPopulator = new InternationalJourneysFieldsetPopulator(
            $radioFieldsetPopulator,
            $niWarningConditionalAdder
        );
 
        $internationalJourneysFieldsetPopulator->populate($form, $fieldset, $options);
    }

    public function dpTrueFalse()
    {
        return [
            [true],
            [false]
        ];
    }
}
