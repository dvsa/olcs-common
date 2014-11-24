<?php

/**
 * Test Checkbox
 *
 * @author Shaun Lizzio Peshkov <shaun.lizzio@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\Checkbox;
use Zend\Validator as ZendValidator;

/**
 * Test Checkbox Input Filter
 *
 * @author Shaun Lizzio Peshkov <shaun.lizzio@valtech.co.uk>
 */
class CheckboxTest extends PHPUnit_Framework_TestCase
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
        $this->element = new Checkbox();
    }

    /**
     * Test validators
     */
    public function testGetInputSpecification()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertEmpty($spec);

        $this->element->setOption('must_be_value', true);
        $spec = $this->element->getInputSpecification();

        $this->assertArrayHasKey('name', $spec);
        $this->assertArrayHasKey('required', $spec);
        $this->assertArrayHasKey('validators', $spec);
        $this->assertTrue($spec['required']);
        $this->assertEquals($spec['validators'][0]['name'], 'Identical');
    }
}
