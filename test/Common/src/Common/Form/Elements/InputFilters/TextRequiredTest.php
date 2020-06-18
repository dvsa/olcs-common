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
class TextRequiredTest extends \PHPUnit\Framework\TestCase
{
    /**
     * test setup
     *
     * @return void
     */
    public function setUp(): void
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
