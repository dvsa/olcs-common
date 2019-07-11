<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\ValidatorsAdder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputInterface;
use Zend\Validator\ValidatorChain;

/**
 * ValidatorsAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ValidatorsAdderTest extends MockeryTestCase
{
    public function testAdd()
    {
        $fieldsetName = 'fields123';

        $betweenValidatorRule = 'Between';
        $betweenValidatorParams = [
            'min' => 5,
            'max' => 10
        ];

        $greaterThanValidatorRule = 'GreaterThan';
        $greaterThanValidatorParams = [
            'min' => 40,
            'inclusive' => true
        ];
         
        $validators = [
            [
                'rule' => $betweenValidatorRule,
                'params' => $betweenValidatorParams
            ],
            [
                'rule' => $greaterThanValidatorRule,
                'params' => $greaterThanValidatorParams
            ]
        ];

        $qaElementValidatorChain = m::mock(ValidatorChain::class);
        $qaElementValidatorChain->shouldReceive('attachByName')
            ->with($betweenValidatorRule, $betweenValidatorParams)
            ->ordered()
            ->once();
        $qaElementValidatorChain->shouldReceive('attachByName')
            ->with($greaterThanValidatorRule, $greaterThanValidatorParams)
            ->ordered()
            ->once();

        $qaElementInput = m::mock(QaElementInput::class);
        $qaElementInput->shouldReceive('setContinueIfEmpty')
            ->with(true)
            ->once();
        $qaElementInput->shouldReceive('getValidatorChain')
            ->andReturn($qaElementValidatorChain);

        $fieldsetInputFilter = m::mock(InputFilterInterface::class);
        $fieldsetInputFilter->shouldReceive('get')
            ->with('qaElement')
            ->andReturn($qaElementInput);

        $qaFieldsetInputFilter = m::mock(InputFilterInterface::class);
        $qaFieldsetInputFilter->shouldReceive('get')
            ->with($fieldsetName)
            ->andReturn($fieldsetInputFilter);

        $formInputFilter = m::mock(InputFilterInterface::class);
        $formInputFilter->shouldReceive('get')
            ->with('qa')
            ->andReturn($qaFieldsetInputFilter);

        $form = m::mock(Form::class);
        $form->shouldReceive('getInputFilter')
            ->withNoArgs()
            ->andReturn($formInputFilter);

        $qaElementInput = m::mock(InputInterface::class);

        $sut = new ValidatorsAdder();
        $sut->add($form, $fieldsetName, $validators);
    }
}
