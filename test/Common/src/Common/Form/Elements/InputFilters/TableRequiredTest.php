<?php

/**
 * Test TableRequired
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\TableRequired;
use Common\Form\Elements\Validators\TableRequiredValidator;

/**
 * Test TableRequired
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableRequiredTest extends PHPUnit_Framework_TestCase
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
        $this->element = new TableRequired();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][0] instanceof TableRequiredValidator);
    }
}
