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
        // validator actually ignores $value and just uses $context
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
            // psvSmallVehicles and psvSmallVhlScotland aren't set - comes back true
            [0, [], true],

            /**
             * if user answers 'yes' in radio control, textarea is mandatory
             * and checkbox is optional
             */
            // psvSmallVehicles = Y, confirmation = blank - comes back true
            [0, ['psvSmallVhlScotland' => 'txt', 'psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => ''], true],

            // psvSmallVehicles = Y, confirmation = N - comes back true
            [0, ['psvSmallVhlScotland' => 'txt', 'psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => 'N'], true],

            // psvSmallVehicles = Y, confirmation = 0 - comes back true
            [0, ['psvSmallVhlScotland' => 'txt', 'psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => '0'], true],

            // psvSmallVehicles = Y, confirmation = Y - comes back true
            [0, ['psvSmallVhlScotland' => 'txt', 'psvOperateSmallVhl' => 'Y', 'psvSmallVhlConfirmation' => 'Y'], true],

            // psvSmallVehicles = set but empty, confirmation = blank - comes back true
            [0, ['psvSmallVhlScotland' => 'txt', 'psvOperateSmallVhl' => null, 'psvSmallVhlConfirmation' => ''], true],

            /**
             * if user answers 'no' in radio control, textarea is optional and
             * checkbox is mandatory
             */
            // psvSmallVehicles = N, confirmation = N - comes back false
            [0, ['psvSmallVhlScotland' => 'txt', 'psvOperateSmallVhl' => 'N', 'psvSmallVhlConfirmation' => 'N'], false],

            // psvSmallVehicles = N, confirmation = blank - comes back false
            [0, ['psvSmallVhlScotland' => 'txt', 'psvOperateSmallVhl' => 'N', 'psvSmallVhlConfirmation' => ''], false],

            // psvSmallVehicles = N, confirmation = Y - comes back true
            [0, ['psvSmallVhlScotland' => 'txt', 'psvOperateSmallVhl' => 'N', 'psvSmallVhlConfirmation' => 'Y'], true],

            /**
             * for Scotland, radio control is not shown so checkbox is always mandatory
             * when psvSmallVhlScotland is present
             */
            // psvSmallVhlScotland exists, psvOperateSmallVhl missing, confirmation = N - comes back false
            [0, ['psvSmallVhlScotland' => 'txt', 'psvSmallVhlConfirmation' => 'N'], false],

            // psvSmallVhlScotland exists, psvOperateSmallVhl missing,  confirmation = blank - comes back false
            [0, ['psvSmallVhlScotland' => 'txt', 'psvSmallVhlConfirmation' => ''], false],

            // psvSmallVhlScotland exists, psvOperateSmallVhl missing,  confirmation = Y - comes back true
            [0, ['psvSmallVhlScotland' => 'txt', 'psvSmallVhlConfirmation' => 'Y'], true],
        ];
    }
}
