<?php

/**
 * Test VehicleSafetyTachographAnalyserContractorValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\VehicleSafetyTachographAnalyserContractorValidator;

/**
 * Test VehicleSafetyTachographAnalyserContractorValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleSafetyTachographAnalyserContractorValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up the validator
     */
    public function setUp(): void
    {
        $this->validator = new VehicleSafetyTachographAnalyserContractorValidator();
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
            array(
                null,
                array('tachographIns' => '', 'tachographInsName' => ''),
                true
            ),
            array(
                null,
                array('tachographIns' => 'tach_internal', 'tachographInsName' => ''),
                true
            ),
            array(
                null,
                array('tachographIns' => 'tach_internal', 'tachographInsName' => 'abc'),
                true
            ),
            array(
                null,
                array('tachographIns' => 'tach_external', 'tachographInsName' => ''),
                false
            ),
            array(
                null,
                array('tachographIns' => 'tach_external', 'tachographInsName' => 'abc'),
                true
            )
        );
    }
}
