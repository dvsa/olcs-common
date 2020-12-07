<?php

namespace CommonTest\Form\Elements\InputFilters;

use Common\Form\Elements\InputFilters;
use \Laminas\Validator\StringLength;

/**
 * Test SelectEmpty InputFilter
 * @covers \Common\Form\Elements\InputFilters\SelectEmpty
 */
class SelectEmptyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * test setup
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->filter = new InputFilters\SelectEmpty("test");
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
     * ensure select option is not required by default
     *
     * @return void
     */
    public function testValueNotRequired()
    {
        $this->assertFalse($this->getSpecificationElement('required'));
    }
}
