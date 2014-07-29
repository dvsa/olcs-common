<?php

/**
 * Test OperatingCentreAdPlacedInValidatorTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\OperatingCentreAdPlacedInValidator;

/**
 * Test OperatingCentreAdPlacedInValidatorTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreAdPlacedInValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new OperatingCentreAdPlacedInValidator();
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
            array('Not bothered', array('adPlaced' => 'N'), true),
            array('', array('adPlaced' => 'Y'), false),
            array('Bob', array('adPlaced' => 'Y'), true)
        );
    }
}
