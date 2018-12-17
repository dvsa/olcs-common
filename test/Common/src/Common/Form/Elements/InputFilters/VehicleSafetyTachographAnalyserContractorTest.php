<?php

/**
 * Test VehicleSafetyTachographAnalyserContractor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use Common\Form\Elements\InputFilters\VehicleSafetyTachographAnalyserContractor;
use Common\Form\Elements\Validators\VehicleSafetyTachographAnalyserContractorValidator;

/**
 * Test VehicleSafetyTachographAnalyserContractor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleSafetyTachographAnalyserContractorTest extends \PHPUnit\Framework\TestCase
{
    /**+
     * Holds the element
     */
    private $element;

    /**
     * Setup the element
     */
    public function setUp()
    {
        $this->element = new VehicleSafetyTachographAnalyserContractor();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][1] instanceof VehicleSafetyTachographAnalyserContractorValidator);
    }
}
