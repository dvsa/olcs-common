<?php

namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\Date;

/**
 * Test DateTest
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class DateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider providerIsValid
     * @param $expected
     * @param $value
     * @param array $errorMessages
     */
    public function testIsValid($expected, $value, $errorMessages = [])
    {
        $errorMessages = empty($errorMessages) ? ['error' => 'message'] : $errorMessages;

        $sut = new Date();
        $this->assertEquals($expected, $sut->isValid($value));

        if (!$expected) {
            $this->assertEquals($errorMessages, $sut->getMessages());
        }
    }

    /**
     * @return array
     */
    public function providerIsValid()
    {
        return [
            // empty date should be invalid
            [
                false,
                null,
                [Date::INVALID => 'Please select a date']
            ],
            // impossible date should be invalid date
            [
                false,
                '2014-02-30',
                [Date::INVALID_DATE => 'The input does not appear to be a valid date']
            ],
            [
                false,
                'invalidinputstring',
                [Date::INVALID_DATE => 'The input does not appear to be a valid date']
            ],
        ];
    }
}
