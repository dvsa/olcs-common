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
class VehicleUndertakingsOperateSmallVehiclesValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new VehicleUndertakingsOperateSmallVehiclesValidator();
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
            // psvSmallVehicles = Y, notes=blank - comes back false
            array(0, array('psvOperateSmallVhl' => 'Y', 'psvSmallVhlNotes' => ''), false),
            // psvSmallVehicles = N, notes=blank - comes back true
            array(0, array('psvOperateSmallVhl' => 'N', 'psvSmallVhlNotes' => ''), true),
            // psvSmallVehicles = Y, notes=something - comes back true
            array(0, array('psvOperateSmallVhl' => 'Y', 'psvSmallVhlNotes' => 'blah blah'), true)
        );
    }
}
