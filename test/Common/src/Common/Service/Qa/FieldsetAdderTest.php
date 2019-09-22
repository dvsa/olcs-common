<?php

namespace CommonTest\Service\Qa;

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
    private $fieldsetName;

    private $fieldset;

    private $fieldsetGenerator;

    private $form;

    private $validatorsAdder;

    private $sut;

    public function setUp()
    {
        $this->fieldsetName = 'fieldsetName';

        $this->fieldset = m::mock(Fieldset::class);
        $this->fieldset->shouldReceive('getName')
            ->andReturn($this->fieldsetName);

        $qaFieldset = m::mock(Fieldset::class);
        $qaFieldset->shouldReceive('add')
            ->with($this->fieldset)
            ->once();

        $this->fieldsetGenerator = m::mock(FieldsetGenerator::class);

        $this->form = m::mock(Form::class);
        $this->form->shouldReceive('get')
            ->with('qa')
            ->andReturn($qaFieldset);

        $this->validatorsAdder = m::mock(ValidatorsAdder::class);

        $this->sut = new FieldsetAdder($this->fieldsetGenerator, $this->validatorsAdder);
    }

    public function testAddWithValidators()
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

        $this->fieldsetGenerator->shouldReceive('generate')
            ->with($this->form, $options)
            ->once()
            ->andReturn($this->fieldset);

        $this->validatorsAdder->shouldReceive('add')
            ->with($this->form, $this->fieldsetName, $validatorsOptions)
            ->once();

        $this->sut->add($this->form, $options);
    }

    public function testAddWithNoValidators()
    {
        $options = [
            'validators' => []
        ];

        $this->fieldsetGenerator->shouldReceive('generate')
            ->with($this->form, $options)
            ->once()
            ->andReturn($this->fieldset);

        $this->validatorsAdder->shouldReceive('add')
            ->never();

        $this->sut->add($this->form, $options);
    }
}
