<?php

/**
 * Test VehicleWeight
 *
 * @author Alex Peshkov <alex.peshkov@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\VehicleWeight;
use Zend\Validator as ZendValidator;

/**
 * Test VehicleWeight Input Filter
 *
 * @author Alex Peshkov <alex.peshkov@clocal.co.uk>
 */
class VehicleWeightTest extends PHPUnit_Framework_TestCase
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
        $this->element = new VehicleWeight();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][0] instanceof ZendValidator\Between);
    }
}
