<?php

/**
 * Test TotalVehicleAuthorityValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\Lva\TotalVehicleAuthorityValidator;

/**
 * Test TotalVehicleAuthorityValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TotalVehicleAuthorityValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new TotalVehicleAuthorityValidator();
    }

    /**
     * Test isValid
     *
     * @group totalVehicleAuthorityValidator
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $totalLicences, $totalVehicleAuthority, $expected)
    {
        $this->validator->setTotalLicences($totalLicences);
        $this->validator->setTotalVehicleAuthority($totalVehicleAuthority);
        $this->assertEquals($expected, $this->validator->isValid($value));
    }

    /**
     * Provider for isValid
     *
     * @return array
     */
    public function providerIsValid()
    {
        return [
            [1, 2, 4, true],
            [1, 2, 3, true],
            [1, 2, 2, false]
        ];
    }
}
