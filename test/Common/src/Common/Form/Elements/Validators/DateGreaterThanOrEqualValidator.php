<?php

/**
 * Test DateGreaterThanOrEqual date validator
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\DateGreaterThanOrEqual;

/**
 * Test DateGreaterThanOrEqual date validator
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class DateGreaterThanOrEqualValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new DateGreaterThanOrEqual('test');
    }

    /**
     * Test isValid
     *
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $context, $expected)
    {
        $this->assertEquals($expected, $this->validator->isValid($value, $context));
    }

    /**
     * Provider for isValid
     *
     * @return array
     */
    public function providerIsValid()
    {
        return array(
            array(
                '2014-01-01',
                array(
                    'test' => array(
                        'year' => '2014',
                        'month' => '01',
                        'day' => '01'
                    )
                ),
                true
            ),
            array(
                '2014-01-30',
                array(
                    'test' => array(
                        'year' => '2014',
                        'month' => '01',
                        'day' => '31'
                    )
                ),
                false
            ),
            array(
                '2013-12-31',
                array(
                    'test' => array(
                        'year' => '2014',
                        'month' => '01',
                        'day' => '01'
                    )
                ),
                false
            ),
        );
    }
}
