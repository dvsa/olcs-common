<?php

/**
 * Test Checkbox
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Custom;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Custom\OlcsCheckbox;
use Zend\Validator as ZendValidator;

/**
 * Test OlcsCheckbox Element
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class OlcsCheckboxTest extends PHPUnit_Framework_TestCase
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
        $this->element = new OlcsCheckbox();
    }

    /**
     * Test validators
     */
    public function testGetInputSpecification()
    {
        $labelPosition = $this->element->getLabelOption('label_position');
        $alwaysWrap = $this->element->getLabelOption('always_wrap');

        $this->assertEquals(
            \Zend\Form\View\Helper\FormRow::LABEL_APPEND,
            $this->element->getLabelOption('label_position')
        );
        $this->assertTrue($this->element->getLabelOption('always_wrap'));

    }
}
