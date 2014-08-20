<?php

/**
 * Test OperatingCentreVehicleAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\OperatingCentreVehicleAuthorisationsValidator;

/**
 * Test OperatingCentreVehicleAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreVehicleAuthorisationsValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new OperatingCentreVehicleAuthorisationsValidator();
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
            // Non numeric
            array('', array(), false),
            // Restricted too many
            array(
                0,
                array('licenceType' => 'ltyp_r', 'totAuthSmallVehicles' => 2, 'totAuthMediumVehicles' => 2),
                false
            ),
            // 0 Operating centres
            array(0, array('noOfOperatingCentres' => 0), false),
            // Total is 0
            array(
                0,
                array('noOfOperatingCentres' => 1, 'totAuthSmallVehicles' => 0, 'totAuthMediumVehicles' => 0),
                false
            ),
            // With 1 oc, total should = min auth
            array(0, array('noOfOperatingCentres' => 1, 'totAuthSmallVehicles' => 1, 'minVehicleAuth' => 5), false),
            // With more than 1 oc, total should be >= min
            array(0, array('noOfOperatingCentres' => 2, 'totAuthSmallVehicles' => 1, 'minVehicleAuth' => 5), false),
            // With more than 1 oc, total should be <= max
            array(
                0,
                array(
                    'noOfOperatingCentres' => 2,
                    'totAuthSmallVehicles' => 15,
                    'minVehicleAuth' => 5,
                    'maxVehicleAuth' => 10
                ),
                false
            ),
            // Boundaries are fine
            array(
                0,
                array(
                    'noOfOperatingCentres' => 2,
                    'totAuthSmallVehicles' => 10,
                    'minVehicleAuth' => 5,
                    'maxVehicleAuth' => 10
                ),
                true
            ),
            array(
                0,
                array(
                    'noOfOperatingCentres' => 2,
                    'totAuthSmallVehicles' => 5,
                    'minVehicleAuth' => 5,
                    'maxVehicleAuth' => 10
                ),
                true
            ),
            // More OC's are fine
            array(
                0,
                array(
                    'noOfOperatingCentres' => 20,
                    'totAuthSmallVehicles' => 10,
                    'minVehicleAuth' => 5,
                    'maxVehicleAuth' => 10
                ),
                true
            ),
            array(
                0,
                array(
                    'noOfOperatingCentres' => 50,
                    'totAuthSmallVehicles' => 5,
                    'minVehicleAuth' => 5,
                    'maxVehicleAuth' => 10
                ),
                true
            ),
        );
    }
}
