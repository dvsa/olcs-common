<?php

namespace CommonTest\Validator;

use Common\Validator\OneOf;

/**
 * Class ValidateDateCompare
 * @package CommonTest\Validator
 */
class OneOfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test setOptions
     */
    public function testSetOptions()
    {
        $sut = new OneOf();
        $sut->setOptions(
            [
                'fields' => ['test', 'test2'],
                'message' => 'Please provide at least one field'
            ]
        );

        $this->assertEquals(['test', 'test2'], $sut->getFields());
        $this->assertEquals(['provide_one' => 'Please provide at least one field'], $sut->getMessageTemplates());
    }

    /**
     * @dataProvider provideIsValid
     * @param $expected
     * @param $options
     * @param $context
     * @param $chainValid
     * @param array $errorMessages
     */
    public function testIsValid($expected, $options, $context)
    {
        $sut = new OneOf();
        $sut->setOptions($options);
        $this->assertEquals($expected, $sut->isValid('', $context));
    }

    /**
     * @return array
     */
    public function provideIsValid()
    {
        return [
            [true, ['fields' => ['test1', 'test2']], ['test1'=>'notempty']],
            [true, ['fields' => ['test1', 'test2']], ['test2'=>'notempty']],
            [true, ['fields' => ['test1', 'test2']], ['test1'=>'notempty', 'test2'=>'notempty']],
            [false, ['fields' => ['test1', 'test2']], ['test1'=>'', 'test2'=>'']],
            [false, ['fields' => ['test1', 'test2']], []]
        ];
    }
}
