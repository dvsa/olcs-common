<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\FieldsetAdder;
use Common\Service\Qa\FieldsetGenerator;
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
        $options = [
            'prop1' => 'value1',
            'prop2' => 'value2'
        ];

        $fieldset = m::mock(Fieldset::class);

        $qaFieldset = m::mock(Fieldset::class);
        $qaFieldset->shouldReceive('add')
            ->with($fieldset)
            ->once();

        $fieldsetGenerator = m::mock(FieldsetGenerator::class);

        $form = m::mock(Form::class);
        $form->shouldReceive('get')
            ->with('qa')
            ->andReturn($qaFieldset);

        $sut = new FieldsetAdder($fieldsetGenerator);

        $fieldsetGenerator->shouldReceive('generate')
            ->with($form, $options)
            ->once()
            ->andReturn($fieldset);

        $sut->add($form, $options);
    }
}
