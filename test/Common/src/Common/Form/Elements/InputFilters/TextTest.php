<?php

/**
 * Test Text InputFilter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use Common\Form\Elements\InputFilters;
use \Zend\Validator\StringLength;

/**
 * Test Text InputFilter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->filter = new InputFilters\Text("test");
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
        $this->assertEquals('test', $this->getSpecificationElement('name'));
    }

    /**
     * ensure text fields aren't required by default
     *
     * @return void
     */
    public function testTextNotRequired()
    {
        $this->assertFalse($this->getSpecificationElement('required'));
    }

    /**
     * ensure validation won't continue on an empty text input
     *
     * @return void
     */
    public function testContinueIfEmptyIsDisabled()
    {
        $this->assertFalse($this->getSpecificationElement('continue_if_empty'));
    }

    /**
     * ensure fields can be left empty
     *
     * @return void
     */
    public function testAllowEmptyIsEnabled()
    {
        $this->assertTrue($this->getSpecificationElement('allow_empty'));
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

    /**
     * Test set allow empty
     *
     * @return void
     */
    public function testSetAllowEmpty()
    {
        $this->filter->setAllowEmpty(false);

        $this->assertFalse($this->getSpecificationElement('allow_empty'));
    }

    /**
     * Test set max
     *
     * @return void
     */
    public function testSetMax()
    {
        $this->filter->setMax(10);

        $this->assertEquals(10, $this->getSpecificationElement('validators')[0]['options']['max']);
    }
}
