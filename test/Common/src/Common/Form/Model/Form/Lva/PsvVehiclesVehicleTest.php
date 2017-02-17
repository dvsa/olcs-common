<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class PsvVehiclesVehicleTest
 *
 * @group FormTests
 */
class PsvVehiclesVehicleTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\PsvVehiclesVehicle::class;

    public function testDataId()
    {
        $element = ['data', 'id'];
        $this->assertFormElementHidden($element);    }

    public function testDataVersion()
    {
        $element = ['data', 'version'];
        $this->assertFormElementHidden($element);    }

    public function testDataVrm()
    {
        $element = ['data', 'vrm'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementVrm($element);
    }

    public function testDataMakeModel()
    {
        $element = ['data', 'makeModel'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 2, 100);
    }
}
