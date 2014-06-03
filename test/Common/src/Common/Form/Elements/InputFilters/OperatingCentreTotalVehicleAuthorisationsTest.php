<?php

/**
 * Test OperatingCentreTotalVehicleAuthorisationsTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\OperatingCentreTotalVehicleAuthorisations;
use Common\Form\Elements\Validators\OperatingCentreTotalVehicleAuthorisationsValidator;
use Zend\Validator as ZendValidator;

/**
 * Test OperatingCentreTotalVehicleAuthorisationsTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreTotalVehicleAuthorisationsTest extends PHPUnit_Framework_TestCase
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
        $this->element = new OperatingCentreTotalVehicleAuthorisations();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][0] instanceof ZendValidator\Digits);
        $this->assertTrue($spec['validators'][1] instanceof ZendValidator\Between);
        $this->assertTrue($spec['validators'][2] instanceof OperatingCentreTotalVehicleAuthorisationsValidator);
    }
}
