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
                array('tachographIns' => '', 'tachographInsName' => ''),
                true
            ),
            array(
                null,
                array('tachographIns' => 'tachograph_analyser.1', 'tachographInsName' => ''),
                true
            ),
            array(
                null,
                array('tachographIns' => 'tachograph_analyser.1', 'tachographInsName' => 'abc'),
                true
            ),
            array(
                null,
                array('tachographIns' => 'tachograph_analyser.2', 'tachographInsName' => ''),
                false
            ),
            array(
                null,
                array('tachographIns' => 'tachograph_analyser.2', 'tachographInsName' => 'abc'),
                true
            )
        );
    }
}
