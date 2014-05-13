<?php

/**
 * Test TextRequired InputFilter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use Common\Form\Elements\InputFilters;

/**
 * Test TextRequired InputFilter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TextRequiredTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->filter = new InputFilters\TextRequired("text-required");
    }

    /**
     * helper to extract a key out of the specification array
     *
     * @param string $key key to extract
     *
     * @return array
     */
    protected function getSpecificationElement($key)
    {
        return $this->filter->getInputSpecification()[$key];
    }

    /**
     * test basic name
     *
     * @return void
     */
    public function testGetInputSpecificationReturnsCorrectName()
    {
        $this->assertEquals('text-required', $this->getSpecificationElement('name'));
    }

    /**
     * ensure fields are required by default
     *
     * @return void
     */
    public function testTextIsRequired()
    {
        $this->assertTrue($this->getSpecificationElement('required'));
    }

    /**
     * ensure fields cannot be left empty
     *
     * @return void
     */
    public function testAllowEmptyIsDisabled()
    {
        $this->assertFalse($this->getSpecificationElement('allow_empty'));
    }

    /**
     * ensure we have no validators by default
     *
     * @return void
     */
    public function testValidatorsAreEmpty()
    {
        $this->assertEquals(array(), $this->getSpecificationElement('validators'));
    }

    /**
     * ensure we trim all input strings
     *
     * @return void
     */
    public function testStringTrimFilterIsUsed()
    {
        $this->assertEquals(
            [['name' => 'Zend\Filter\StringTrim']],
            $this->getSpecificationElement('filters')
        );
    }
}
