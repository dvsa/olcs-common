<?php

/**
 * Test VehicleUndertakingsOperateSmallVehiclesValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesValidator;

/**
 * Test VehicleUndertakingsOperateSmallVehiclesValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class VehicleUndertakingsOperateSmallVehiclesValidatorTest extends \PHPUnit\Framework\TestCase
{
    public $validator;
    /**
     * Set up the validator
     */
    protected function setUp(): void
    {
        $this->validator = new VehicleUndertakingsOperateSmallVehiclesValidator();
    }

    /**
     * Test isValid
     *
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $context, $expected): void
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
            // psvSmallVehicles = Y, notes=blank - comes back false
            [0, ['psvOperateSmallVhl' => 'Y', 'psvSmallVhlNotes' => ''], false],
            // psvSmallVehicles = Y, notes=something - comes back true
            [0, ['psvOperateSmallVhl' => 'Y', 'psvSmallVhlNotes' => 'blah blah'], true],
            // psvSmallVehicles = N, notes=blank - comes back true
            [0, ['psvOperateSmallVhl' => 'N', 'psvSmallVhlNotes' => ''], true],
            // psvSmallVehicles = N, notes=something - comes back true (although irrelevant)
            [0, ['psvOperateSmallVhl' => 'N', 'psvSmallVhlNotes' => 'foo bar'], true],
        ];
    }
}
