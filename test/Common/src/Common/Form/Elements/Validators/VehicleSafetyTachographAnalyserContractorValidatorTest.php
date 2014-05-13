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
class VehicleSafetyTachographAnalyserContractorValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
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
                array('licence.tachographIns' => '', 'licence.tachographInsName' => ''),
                true
            ),
            array(
                null,
                array('licence.tachographIns' => 'tachograph_analyser.1', 'licence.tachographInsName' => ''),
                true
            ),
            array(
                null,
                array('licence.tachographIns' => 'tachograph_analyser.1', 'licence.tachographInsName' => 'abc'),
                true
            ),
            array(
                null,
                array('licence.tachographIns' => 'tachograph_analyser.2', 'licence.tachographInsName' => ''),
                false
            ),
            array(
                null,
                array('licence.tachographIns' => 'tachograph_analyser.2', 'licence.tachographInsName' => 'abc'),
                true
            )
        );
    }
}
