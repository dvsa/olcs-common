<?php

/**
 * Test Vehicle Undertakings Operate Small Vehicles Agreement Validator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesAgreementValidator;

/**
 * Test Vehicle Undertakings Operate Small Vehicles Agreement Validator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleUndertakingsOperateSmallVehiclesAgreementValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
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
            // no context means scotland, therefore required
            ['N', [], false],
            ['Y', [], true],
            // not scottish but agreed to the operate small vhl terms, not required
            ['N', ['psvOperateSmallVhl' => 'Y'], true],
            ['Y', ['psvOperateSmallVhl' => 'Y'], true],
            // not scottish, not agreed to the operate small vhl terms, required
            ['Y', ['psvOperateSmallVhl' => 'N'], true],
            ['N', ['psvOperateSmallVhl' => 'N'], false]
        ];
    }
}
