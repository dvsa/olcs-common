<?php

/**
 * Test OperatingCentreDateAdPlacedValidatorTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\OperatingCentreDateAdPlacedValidator;

/**
 * Test OperatingCentreDateAdPlacedValidatorTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreDateAdPlacedValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new OperatingCentreDateAdPlacedValidator();
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
            array('Invalid Date', array('adPlaced' => 'Y'), false),
            array('2014-01-01', array('adPlaced' => 'Y'), true)
        );
    }
}
