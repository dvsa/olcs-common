<?php

/**
 * Test Withdrawn date validator
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\WithdrawnDate;

/**
 * Test Withdrawn date validator
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class WithdrawnDateValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new WithdrawnDate();
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
                    'isWithdrawn' => 'Y',
                    'withdrawnDate' => array(
                        'year' => '2014',
                        'month' => '01',
                        'day' => '01'
                    )
                ),
                true
            ),
            array(
                '2014-01-32',
                array(
                    'isWithdrawn' => 'Y',
                    'withdrawnDate' => array(
                        'year' => '2014',
                        'month' => '01',
                        'day' => '32'
                    )
                ),
                false
            ),
            array(
                '2014-02-30',
                array(
                    'isWithdrawn' => 'Y',
                    'withdrawnDate' => array(
                        'year' => '2014',
                        'month' => '02',
                        'day' => '30'
                    )
                ),
                false
            ),
            array(
                '2100-12-31',
                array(
                    'isWithdrawn' => 'Y',
                    'withdrawnDate' => array(
                        'year' => '2100',
                        'month' => '12',
                        'day' => '31'
                    )
                ),
                false
            ),
            array(
                null,
                array(
                    'isWithdrawn' => 'N',
                    'withdrawnDate' => array(
                        'year' => '',
                        'month' => '',
                        'day' => ''
                    )
                ),
                true
            )
        );
    }
}
