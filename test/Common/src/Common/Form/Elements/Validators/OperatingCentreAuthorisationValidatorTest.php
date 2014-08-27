<?php

/**
 * Test OperatingCentreAuthorisationValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\OperatingCentreAuthorisationValidator;
use PHPUnit_Framework_TestCase;

/**
 * Test OperatingCentreAuthorisationValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreAuthorisationValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new OperatingCentreAuthorisationValidator();
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
                0,
                array('noOfTrailersPossessed' => 1, 'noOfVehiclesPossessed' => 1),
                true
            ),
            array(
                0,
                array('noOfTrailersPossessed' => 0, 'noOfVehiclesPossessed' => 1),
                true
            ),
            array(
                0,
                array('noOfTrailersPossessed' => 1, 'noOfVehiclesPossessed' => 0),
                true
            ),
            array(
                0,
                array('noOfTrailersPossessed' => 0, 'noOfVehiclesPossessed' => 0),
                false
            ),
            array(
                0,
                array('noOfVehiclesPossessed' => 1),
                true
            ),
            array(
                0,
                array('noOfVehiclesPossessed' => 0),
                false
            )
        );
    }
}
