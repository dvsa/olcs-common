<?php

/**
 * Dynamic Multi Select Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Types;

use PHPUnit_Framework_TestCase;
use Common\Form\Element\DynamicMultiSelect;

/**
 * Dynamic Multi Select Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DynamicMultiSelectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Dynamic multi select test
     * @group dynamicMultiSelect
     */
    public function testElement()
    {
        $options = [
            'chosen-size' => 'medium'
        ];
        $element = new DynamicMultiSelect('name', $options);
        $this->assertEquals($element->getAttribute('class'), 'chosen-select-medium');
        $this->assertEquals($element->getAttribute('multiple'), 'multiple');
        $this->assertInstanceOf('Common\Form\Element\DynamicMultiSelect', $element);
    }
}
