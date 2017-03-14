<?php

namespace CommonTest\Form\Model\Form\Lva;

use Common\Form\Elements\InputFilters\SingleCheckbox;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Form\Element\Radio;

/**
 * Class SafetyTest
 *
 * @group FormTests
 */
class SafetyTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\Safety::class;

    public function testVersion()
    {
        $element = ['licence', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testSafetyInsVehicle()
    {
        $element = ['licence', 'safetyInsVehicles'];
        $this->assertFormElementNumber($element, 1, 13, ['notBetween']);
    }

    public function testSafetyInsTrailers()
    {
        $element = ['licence', 'safetyInsTrailers'];
        $this->assertFormElementNumber($element, 1, 13, ['notBetween']);
    }

    public function testSafetyInsVaries()
    {
        $element = ['licence', 'safetyInsVaries'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testTacographIns()
    {
        $element = ['licence', 'tachographIns'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 'tach_internal');
        $this->assertFormElementValid($element, 'tach_external');
        $this->assertFormElementValid($element, 'tach_na');
    }

    public function testTacographInsName()
    {
        $element = ['licence', 'tachographInsName'];
        $this->assertFormElementRequired($element, false);
    }

    public function testTable()
    {
        $element = ['table', 'table'];
        $this->assertFormElementTable($element);
        $this->assertFormElementNotValid($element, null, ['required']);


        $element = ['table', 'action'];
        $this->assertFormElementHidden($element);

        $element = ['table', 'rows'];
        $this->assertFormElementHidden($element);

        $element = ['table', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testApplicationVersion()
    {
        $element = ['application', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testApplicationIsMaintenanceSuitable()
    {
        $element = ['application', 'isMaintenanceSuitable'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testApplicationSafetyConfirmation()
    {
        $element = ['application', 'safetyConfirmation'];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testSaveAndContinue()
    {
        $element = ['form-actions', 'saveAndContinue'];
        $this->assertFormElementActionButton($element);
    }

    public function testSave()
    {
        $element = ['form-actions', 'save'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
