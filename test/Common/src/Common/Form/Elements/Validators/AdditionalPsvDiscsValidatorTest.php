<?php

/**
 * Additional Psv Discs Validator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Validators\AdditionalPsvDiscsValidator;

/**
 * Additional Psv Discs Validator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AdditionalPsvDiscsValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Subject under test
     *
     * @var \Common\Form\Elements\Validators\AdditionalPsvDiscsValidator
     */
    private $sut;

    /**
     * test setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->sut = new AdditionalPsvDiscsValidator();
    }

    /**
     * @group validators
     * @group additional_psv_discs_validators
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $context, $expected)
    {
        $this->assertEquals($expected, $this->sut->isValid($value, $context));
    }

    public function providerIsValid()
    {
        return array(
            array(
                9,
                array(
                    'totalAuth' => 20,
                    'discCount' => 10
                ),
                true
            ),
            array(
                10,
                array(
                    'totalAuth' => 20,
                    'discCount' => 10
                ),
                true
            ),
            array(
                11,
                array(
                    'totalAuth' => 20,
                    'discCount' => 10
                ),
                false
            ),
            array(
                19,
                array(
                    'totalAuth' => 20,
                    'discCount' => 0
                ),
                true
            ),
            array(
                20,
                array(
                    'totalAuth' => 20,
                    'discCount' => 0
                ),
                true
            ),
            array(
                21,
                array(
                    'totalAuth' => 20,
                    'discCount' => 0
                ),
                false
            ),
            array(
                0,
                array(
                    'totalAuth' => 0,
                    'discCount' => 0
                ),
                true
            ),
            array(
                1,
                array(
                    'totalAuth' => 0,
                    'discCount' => 0
                ),
                false
            )
        );
    }
}
