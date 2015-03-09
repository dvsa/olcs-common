<?php

/**
 * Operating Centre Total Vehicle Authorisations Psv Restricted Validator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Validators\OcTotVehicleAuthPsvRestrictedValidator;

/**
 * Operating Centre Total Vehicle Authorisations Psv Restricted Validator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OcTotVehicleAuthPsvRestrictedValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new OcTotVehicleAuthPsvRestrictedValidator();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($value, $expected)
    {
        $this->assertEquals($expected, $this->sut->isValid($value));
    }

    public function provider()
    {
        return [
            [
                1,
                true
            ],
            [
                2,
                true
            ],
            [
                3,
                false
            ]
        ];
    }
}
