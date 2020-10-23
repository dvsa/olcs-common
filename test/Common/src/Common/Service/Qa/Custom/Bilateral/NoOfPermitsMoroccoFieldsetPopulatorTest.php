<?php

namespace CommonTest\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\Custom\Bilateral\NoOfPermitsElement;
use Common\Service\Qa\Custom\Bilateral\NoOfPermitsMoroccoFieldsetPopulator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * NoOfPermitsMoroccoFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsMoroccoFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $label = 'textbox.label';
        $value = '45';

        $options = [
            'label' => $label,
            'value' => $value,
        ];

        $expectedParams = [
            'type' => NoOfPermitsElement::class,
            'name' => 'qaElement',
            'options' => [
                'label' => $label,
            ],
            'attributes' => [
                'value' => $value
            ]
        ];

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($expectedParams)
            ->once();

        $form = m::mock(Form::class);

        $noOfPermitsMoroccoFieldsetPopulator = new NoOfPermitsMoroccoFieldsetPopulator();

        $noOfPermitsMoroccoFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
