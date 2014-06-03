<?php

/**
 * Test OperatingCentreTrailerAuthorisations
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\OperatingCentreTrailerAuthorisations;
use Common\Form\Elements\Validators\OperatingCentreTrailerAuthorisationsValidator;
use Zend\Validator as ZendValidator;

/**
 * Test OperatingCentreTrailerAuthorisations
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreTrailerAuthorisationsTest extends PHPUnit_Framework_TestCase
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
        $this->element = new OperatingCentreTrailerAuthorisations();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][0] instanceof ZendValidator\Digits);
        $this->assertTrue($spec['validators'][1] instanceof ZendValidator\Between);
        $this->assertTrue($spec['validators'][2] instanceof OperatingCentreTrailerAuthorisationsValidator);
    }
}
