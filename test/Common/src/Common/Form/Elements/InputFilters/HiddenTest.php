<?php

namespace CommonTest\Form\Elements\InputFilters;

use Common\Form\Elements\InputFilters;
use \Zend\Validator\StringLength;

/**
 * Test Hidden InputFilter
 * @covers \Common\Form\Elements\InputFilters\Hidden
 */
class HiddenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->filter = new InputFilters\Hidden("test");
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
     * ensure hidden fields aren't required by default
     *
     * @return void
     */
    public function testTextNotRequired()
    {
        $this->assertFalse($this->getSpecificationElement('required'));
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
     * Test set max
     *
     * @return void
     */
    public function testSetMax()
    {
        $this->filter->setMax(10);

        $this->assertEquals(10, $this->getSpecificationElement('validators')[1]['options']['max']);
    }
}
