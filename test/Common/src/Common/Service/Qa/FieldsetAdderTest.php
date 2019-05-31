<?php

namespace CommonTest\Form\View\Helper;

use Common\Service\Qa\FieldsetAdder;
use Common\Service\Qa\FieldsetGenerator;
use Common\Service\Qa\ValidatorsAdder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * FieldsetAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FieldsetAdderTest extends MockeryTestCase
{
    public function testAdd()
    {
        $validatorsOptions = [
            [
                'rule' => 'Between',
                'params' => [
                    'min' => 42,
                    'max' => 50
                ]
            ]
        ];

        $options = [
            'validators' => $validatorsOptions
        ];

        $fieldsetName = 'fieldsetName';

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('getName')
            ->andReturn($fieldsetName);

        $fieldsetGenerator = m::mock(FieldsetGenerator::class);
        $fieldsetGenerator->shouldReceive('generate')
            ->with($options)
            ->once()
            ->andReturn($fieldset);

        $form = m::mock(Form::class);
        $form->shouldReceive('add')
            ->with($fieldset)
            ->once();

        $validatorsAdder = m::mock(ValidatorsAdder::class);
        $validatorsAdder->shouldReceive('add')
            ->with($form, $fieldsetName, $validatorsOptions)
            ->once();

        $sut = new FieldsetAdder($fieldsetGenerator, $validatorsAdder);
        $sut->add($form, $options);
    }
}
