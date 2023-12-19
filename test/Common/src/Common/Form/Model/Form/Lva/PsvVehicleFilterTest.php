<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;
use Laminas\Form\Element\Select;
use Common\Form\Elements\Custom\OlcsCheckbox;

/**
 * Class PsvVehicleFilterTest
 *
 * @group FormTests
 */
class PsvVehicleFilterTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\PsvVehicleFilter::class;

    public function testVrm()
    {
        $element = ['vrm'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testSpecified()
    {
        $element = ['specified'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testDisc()
    {
        $element = ['disc'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testIncludeRemoved()
    {
        $element = ['includeRemoved'];
        $this->assertFormElementType($element, OlcsCheckbox::class);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testLimit()
    {
        $element = ['limit'];
        $this->assertFormElementHidden($element);
    }

    public function testFilterButton()
    {
        $element = ['filter'];
        $this->assertFormElementActionButton($element);
    }
}
