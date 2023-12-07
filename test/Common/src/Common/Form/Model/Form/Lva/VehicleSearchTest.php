<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class VehicleSearchTest
 *
 * @group FormTests
 */
class VehicleSearchTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\VehicleSearch::class;

    public function testIncludeRemoved()
    {
        $element = ['includeRemoved'];
        $this->assertFormElementHidden($element);
    }

    public function testLimit()
    {
        $element = ['limit'];
        $this->assertFormElementHidden($element);
    }

    public function testVehicleVrm()
    {
        $element = ['vehicleSearch', 'vrm'];
        $this->assertFormElementText($element);
        $this->assertFormElementRequired($element, true);
    }

    public function testFilterButton()
    {
        $element = ['vehicleSearch', 'filter'];
        $this->assertFormElementActionButton($element);
    }

    public function testClearSearchButton()
    {
        $element = ['vehicleSearch', 'clearSearch'];
        $this->assertFormElementActionButton($element);
    }
}
