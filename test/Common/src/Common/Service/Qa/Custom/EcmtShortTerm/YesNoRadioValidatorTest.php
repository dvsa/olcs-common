<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\Custom\EcmtShortTerm\YesNoRadioValidator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * YesNoRadioValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class YesNoRadioValidatorTest extends MockeryTestCase
{
    private $form;

    private $fieldsetName;

    private $yesNoRadioValidator;

    public function setUp()
    {
        $this->form = m::mock(Form::class);

        $this->fieldsetName = 'fieldset12';

        $this->yesNoRadioValidator = new YesNoRadioValidator($this->form, $this->fieldsetName);
    }

    public function testRemoveRequiredWhenValueZero()
    {
        $yesContentInput = m::mock(Input::class);
        $yesContentInput->shouldReceive('setRequired')
            ->with(false)
            ->once();

        $fieldsetInput = m::mock(Input::class);
        $fieldsetInput->shouldReceive('get')
            ->with('yesContent')
            ->andReturn($yesContentInput);

        $qaInput = m::mock(Input::class);
        $qaInput->shouldReceive('get')
            ->with($this->fieldsetName)
            ->andReturn($fieldsetInput);

        $inputFilter = m::mock(InputFilter::clasS);
        $inputFilter->shouldReceive('get')
            ->with('qa')
            ->andReturn($qaInput);

        $this->form->shouldReceive('getInputFilter')
            ->andReturn($inputFilter);

        $this->assertTrue(
            $this->yesNoRadioValidator->isValid(0)
        );
    }

    public function testNoActionWhenValueNotZero()
    {
        $this->assertTrue(
            $this->yesNoRadioValidator->isValid(1)
        );
    }
}
