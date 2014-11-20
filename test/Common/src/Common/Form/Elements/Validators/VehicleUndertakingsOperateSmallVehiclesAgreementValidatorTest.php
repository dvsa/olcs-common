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
        return [
            // psvSmallVehicles isn't set - comes back true
            [0, [], true],

            // psvSmallVehicles = Y, confirmation = blank - comes back false
            [0, ['psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => ''], false],

            // psvSmallVehicles = Y, confirmation = N - comes back false
            [0, ['psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => 'N'], false],

            // psvSmallVehicles = Y, confirmation = 0 - comes back false
            [0, ['psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => '0'], false],

            // psvSmallVehicles = Y, confirmation = Y - comes back true
            [0, ['psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => 'Y'], true],

            // psvSmallVehicles = N, confirmation = N - comes back true
            [0, ['psvOperateSmallVhl' => 'N', 'psvSmallVhlConfirmation' => 'N'], true],

            // psvSmallVehicles = N, confirmation = blank - comes back true
            [0, ['psvOperateSmallVhl' => 'N', 'psvSmallVhlConfirmation' => ''], true]
        ];
    }
}
