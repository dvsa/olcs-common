<?php

/**
 * Test Vehicle Undertakings Operate Small Vehicles Agreement Validator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesAgreementValidator;

/**
 * Test Vehicle Undertakings Operate Small Vehicles Agreement Validator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleUndertakingsOperateSmallVehiclesAgreementValidatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->validator = new VehicleUndertakingsOperateSmallVehiclesAgreementValidator();
    }

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $context, $expected)
    {
        $this->assertEquals($expected, $this->validator->isValid($value, $context));
    }

    public function providerIsValid()
    {
        return [
            ['N', [], true],
            ['N', ['psvOperateSmallVhl' => 'Y'], true],
            ['N', ['psvOperateSmallVhl' => 'N'], false],
            ['Y', [], true],
            ['Y', ['psvOperateSmallVhl' => 'Y'], true],
            ['Y', ['psvOperateSmallVhl' => 'N'], true]
        ];
    }
}
