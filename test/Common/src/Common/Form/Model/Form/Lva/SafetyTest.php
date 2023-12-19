<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Common\Form\Elements\InputFilters\SingleCheckbox;
use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;
use Laminas\Form\Element\Radio;

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
        $this->assertFormElementIsRequired($element, true);
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
        $this->assertFormElementIsRequired($element, false);
    }

    public function testTable()
    {
        $this->assertFormElementTable(['table', 'table']);
        $this->assertFormElementHidden(['table', 'action']);
        $this->assertFormElementHidden(['table', 'id']);

        $element = ['table', 'rows'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementIsRequired($element);
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
        $this->assertFormElementIsRequired($element, true);
    }

    public function testApplicationSafetyConfirmation()
    {
        $element = ['application', 'safetyConfirmation'];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementIsRequired($element, true);
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
