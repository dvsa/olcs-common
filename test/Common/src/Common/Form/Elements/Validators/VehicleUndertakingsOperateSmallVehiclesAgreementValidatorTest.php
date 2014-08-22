<?php

/**
 * Test VehicleUndertakingsOperateSmallVehiclesAgreementValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesAgreementValidator;

/**
 * Test VehicleUndertakingsOperateSmallVehiclesAgreementValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class VehicleUndertakingsOperateSmallVehiclesAgreementValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new VehicleUndertakingsOperateSmallVehiclesAgreementValidator();
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
            // psvSmallVehicles isn't set - comes back true
            array(0, array(), true),
            // psvSmallVehicles = Y, confirmation=blank - comes back true
            array(0, array('psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => ''), true),
            // psvSmallVehicles = Y, confirmation=1 - comes back true
            array(0, array('psvOperateSmallVhl' => 'N', 'psvSmallVhlConfirmation' => '1'), true),
            // psvSmallVehicles = Y, confirmation=0 - comes back true
            array(0, array('psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => '0'), true),
            // psvSmallVehicles = N, confirmation=0 - comes back false
            array(0, array('psvOperateSmallVhl' => 'N', 'psvSmallVhlConfirmation' => '0'), false),
            // psvSmallVehicles = N, confirmation=blank - comes back false
            array(0, array('psvOperateSmallVhl' => 'N', 'psvSmallVhlConfirmation' => ''), false)
        );
    }
}
