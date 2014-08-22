<?php

/**
 * Test OperatingCentreVehicleAuthorisations
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\OperatingCentreVehicleAuthorisations;
use Common\Form\Elements\Validators\OperatingCentreVehicleAuthorisationsValidator;
use Zend\Validator as ZendValidator;

/**
 * Test OperatingCentreVehicleAuthorisations
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreVehicleAuthorisationsTest extends PHPUnit_Framework_TestCase
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
        $this->element = new OperatingCentreVehicleAuthorisations();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][0] instanceof OperatingCentreVehicleAuthorisationsValidator);
        $this->assertTrue($spec['validators'][1] instanceof ZendValidator\Between);
    }
}
