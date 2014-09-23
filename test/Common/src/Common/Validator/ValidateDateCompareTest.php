<?php

namespace CommonTest\Validator;

use Common\Validator\DateCompare;
use Mockery as m;

/**
 * Class ValidateDateCompare
 * @package CommonTest\Validator
 */
class ValidateDateComparefTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test setOptions
     */
    public function testSetOptions()
    {
        $sut = new DateCompare();
        $sut->setOptions([
            'compare_to' =>'test',
            'operator' => false,
            'compare_to_label' => [null]
        ]);

        $this->assertEquals('test', $sut->getCompareTo());
        $this->assertEquals([null], $sut->getCompareToLabel());
        $this->assertEquals(false, $sut->getOperator());
    }

    /**
     * @dataProvider provideIsValid
     * @param $expected
     * @param $options
     * @param $context
     * @param $chainValid
     * @param array $errorMessages
     */
    public function testIsValid($expected, $options, $value, $context, $errorMessages = [])
    {
        $errorMessages = empty($errorMessages) ? ['error' => 'message'] : $errorMessages;

        $sut = new DateCompare();
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
                [true,
                    ['compare_to' => 'other_field', 'operator' => 'gt', 'compare_to_label' => 'Other field'],
                    '2014-01-10',
                    ['other_field'=>
                        ['day' => '09', 'month' => '01', 'year' => '2014'], true],
                    true
                ],
                //context matches, field is invalid
                [false,
                    ['compare_to' => 'other_field', 'operator' => 'gt', 'compare_to_label' => 'Other field'],
                    '2014-01-10',
                    ['other_field'=> ['day' => '11', 'month' => '01', 'year' => '2014']],
                    ['notGreaterThan' => 'This date must be after \'Other field\'']],

            //context doesn't match, field is invalid
                [false,
                    ['compare_to' => 'other_field', 'operator' => 'gt', 'compare_to_label' => 'Other field'],
                    '2014-01-10',
                    [],
                    ['context field not in input' => NULL]
                ],
            //missing context
            [false,
                ['compare_to' => 'other_field', 'operator' => 'gt', 'compare_to_label' => 'Other field'],
                '2014-01-10',
                [],
                ['context field not in input' => NULL]
            ],
            //context matches value is empty
            [false,
                ['compare_to' => 'other_field', 'operator' => 'gt', 'compare_to_label' => 'Other field'],
                '',
                ['other_field'=> ['day' => '11', 'month' => '01', 'year' => '2014']],
                ['notGreaterThan' => 'This date must be after \'Other field\'']],
        ];
    }
}
