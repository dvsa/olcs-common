<?php

/**
 * Test OperatingCentreTrailerAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\OperatingCentreTrailerAuthorisationsValidator;

/**
 * Test OperatingCentreTrailerAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreTrailerAuthorisationsValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new OperatingCentreTrailerAuthorisationsValidator();
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
            // No OCs
            array(0, array('noOfOperatingCentres' => 0, 'minTrailerAuth' => 0, 'maxTrailerAuth' => 0), true), // note this is different to vehicles(!)
            array(1, array('noOfOperatingCentres' => 0, 'minTrailerAuth' => 0, 'maxTrailerAuth' => 0), false),
            // 1 OC
            array(9, array('noOfOperatingCentres' => 1, 'minTrailerAuth' => 10, 'maxTrailerAuth' => 10), false),
            array(11, array('noOfOperatingCentres' => 1, 'minTrailerAuth' => 10, 'maxTrailerAuth' => 10), false),
            array(10, array('noOfOperatingCentres' => 1, 'minTrailerAuth' => 10, 'maxTrailerAuth' => 10), true),
            // Multiple OC's
            array(9, array('noOfOperatingCentres' => 5, 'minTrailerAuth' => 10, 'maxTrailerAuth' => 50), false),
            array(10, array('noOfOperatingCentres' => 5, 'minTrailerAuth' => 10, 'maxTrailerAuth' => 50), true),
            array(30, array('noOfOperatingCentres' => 5, 'minTrailerAuth' => 10, 'maxTrailerAuth' => 50), true),
            array(50, array('noOfOperatingCentres' => 5, 'minTrailerAuth' => 10, 'maxTrailerAuth' => 50), true),
            array(51, array('noOfOperatingCentres' => 5, 'minTrailerAuth' => 10, 'maxTrailerAuth' => 50), false)
        );
    }
}
