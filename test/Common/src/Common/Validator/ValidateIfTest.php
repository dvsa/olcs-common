<?php

namespace CommonTest\Validator;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Validator\ValidateIf;
use Mockery as m;

/**
 * Class ValidateIfTest
 * @package CommonTest\Validator
 */
class ValidateIfTest extends MockeryTestCase
{
    /**
     *
     */
    public function testSetOptions()
    {
        $sut = new ValidateIf();
        $sut->setOptions(
            [
                'context_field' =>'test',
                'context_truth' => false,
                'context_values' => [null],
                'allow_empty' => true
            ]
        );

        $this->assertEquals('test', $sut->getContextField());
        $this->assertEquals([null], $sut->getContextValues());
        $this->assertEquals(false, $sut->getContextTruth());
        $this->assertEquals(true, $sut->allowEmpty());
    }

    /**
     *
     */
    public function testGetValidatorChain()
    {
        $mockValidator = m::mock('Zend\Validator\NotEmpty');

        $mockValidatorPluginManager = m::mock('Zend\Validator\ValidatorPluginManager');
        $mockValidatorPluginManager->shouldReceive('get')->with('NotEmpty', [])->andReturn($mockValidator);

        $sut = new ValidateIf();
        $sut->setValidatorPluginManager($mockValidatorPluginManager);
        $sut->setValidators([['name'=>'NotEmpty']]);

        $validatorChain = $sut->getValidatorChain();
        $this->assertInstanceOf('Zend\Validator\ValidatorChain', $validatorChain);
        $this->assertSame($validatorChain, $sut->getValidatorChain());

        $this->assertCount(1, $validatorChain->getValidators());
        $this->assertSame($mockValidatorPluginManager, $validatorChain->getPluginManager());
    }

    /**
     * @dataProvider provideIsValid
     * @param $expected
     * @param $options
     * @param $context
     * @param $chainValid
     * @param array $errorMessages
     */
    public function testIsValid($expected, $options, $value, $context, $chainValid, $errorMessages = [])
    {
        $errorMessages = empty($errorMessages) ? ['error' => 'message'] : $errorMessages;

        $mockValidatorChain = m::mock('Zend\Validator\ValidatorChain');
        $mockValidatorChain->shouldReceive('isValid')->with($value, $context)->andReturn($chainValid);
        $mockValidatorChain->shouldReceive('getMessages')->andReturn($errorMessages);

        $sut = new ValidateIf();
        $sut->setValidatorChain($mockValidatorChain);
        $sut->setOptions($options);
        $this->assertEquals($expected, $sut->isValid($value, $context));

        if (!$expected) {
            $this->assertEquals($errorMessages, $sut->getMessages());
        }
    }

    /**
     * @return array
     */
    public function provideIsValid()
    {
        return [
            //context matches, field is valid
            [true, ['context_field' => 'field', 'context_values' => ['Y']], 'isValid', ['field'=>'Y'], true],
            //context matches, field is invalid
            [false, ['context_field' => 'field', 'context_values' => ['Y']], 'isValid', ['field'=>'Y'], false],
            //context doesn't match, field is invalid
            [true, ['context_field' => 'field', 'context_values' => ['Y']], 'isValid', ['field'=>'N'], false],
            //inverse context match, field valid
            [
                true,
                ['context_field' => 'field', 'context_values' => ['Y'], 'context_truth' => 0],
                'isValid',
                ['field'=>'N'],
                true
            ],
            //missing context
            [false, [], 'isValid', [], false, ['no_context' => 'Context field was not found in the input']],
            //context matches value is empty
            [
                true,
                ['allow_empty'=>true, 'context_field' => 'field', 'context_values' => ['Y']],
                null,
                ['field'=>'Y'],
                true
            ],
        ];
    }
}
