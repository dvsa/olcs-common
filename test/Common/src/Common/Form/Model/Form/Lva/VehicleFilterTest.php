<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\Form\Element\Select;
use Common\Form\Elements\Custom\OlcsCheckbox;

/**
 * Class VehicleFilterTest
 *
 * @group FormTests
 */
class VehicleFilterTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\VehicleFilter::class;

    public function testVrm()
    {
        $element = ['vrm'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testSpecified()
    {
        $element = ['specified'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 'A');
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
    }

    public function testDisc()
    {
        $element = ['disc'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 'A');
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
    }

    public function testIncludeRemoved()
    {
        $element = ['includeRemoved'];
        $this->assertFormElementType($element, OlcsCheckbox::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testLimit()
    {
        $element = ['limit'];
        $this->assertFormElementHidden($element);
    }

    public function testFilter()
    {
        $element = ['filter'];
        $this->assertFormElementActionButton($element);
    }
}
