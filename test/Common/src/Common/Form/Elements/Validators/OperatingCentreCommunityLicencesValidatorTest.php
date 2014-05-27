<?php

/**
 * Test OperatingCentreCommunityLicencesValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\OperatingCentreCommunityLicencesValidator;

/**
 * Test OperatingCentreCommunityLicencesValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreCommunityLicencesValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new OperatingCentreCommunityLicencesValidator();
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
            array('', array(), false),
            array(
                10,
                array('noOfOperatingCentres' => 1, 'totAuthSmallVehicles' => 3, 'totAuthMediumVehicles' => 3),
                false
            ),
            array(
                6,
                array('noOfOperatingCentres' => 1, 'totAuthSmallVehicles' => 3, 'totAuthMediumVehicles' => 3),
                true
            ),
            array(
                3,
                array('noOfOperatingCentres' => 1, 'totAuthSmallVehicles' => 3, 'totAuthMediumVehicles' => 3),
                true
            ),
        );
    }
}
