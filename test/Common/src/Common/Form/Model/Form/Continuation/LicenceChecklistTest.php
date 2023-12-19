<?php

namespace CommonTest\Common\Form\Model\Form\Continuation;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;
use Common\Form\Model\Form\Continuation\LicenceChecklist;
use Laminas\Validator\Identical;

/**
 * Class LicenceChecklist
 *
 * @group FormTests
 */
class LicenceChecklistTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = LicenceChecklist::class;

    public function testTypeOfLicenceCheckbox()
    {
        $element = ['data', 'typeOfLicenceCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testBusinessTypeCheckbox()
    {
        $element = ['data', 'businessTypeCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testBusinessDetailsCheckbox()
    {
        $element = ['data', 'businessDetailsCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testAddressesCheckbox()
    {
        $element = ['data', 'addressesCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testPeopleCheckbox()
    {
        $element = ['data', 'peopleCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testOperatingCentresCheckbox()
    {
        $element = ['data', 'operatingCentresCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testTransportManagersCheckbox()
    {
        $element = ['data', 'transportManagersCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testVehiclesCheckbox()
    {
        $element = ['data', 'vehiclesCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testSafetyCheckbox()
    {
        $element = ['data', 'safetyCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testUsersCheckbox()
    {
        $element = ['data', 'usersCheckbox'];
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testLicenceChecklistConfirmation()
    {
        $element = ['licenceChecklistConfirmation', 'yesNo'];
        $this->assertFormElementIsRequired($element, true, [0 => 0]);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
        $this->assertFormElementNotValid($element, 'X', 'continuations.checklist.confirmation.error');
        $this->assertFormElementNotValid($element, '', 'continuations.checklist.confirmation.error');
    }

    public function testSubmit()
    {
        $element = ['licenceChecklistConfirmation', 'yesContent', 'submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testChecklistConfirmText()
    {
        $element = ['licenceChecklistConfirmation', 'yesContent', 'checklistConfirmText'];
        $this->assertFormElementHtml($element);
    }

    public function testChecklistDeclineText()
    {
        $element = ['licenceChecklistConfirmation', 'noContent', 'checklistDeclineText'];
        $this->assertFormElementHtml($element);
    }

    public function testActionLink()
    {
        $element = ['licenceChecklistConfirmation', 'noContent', 'backToLicence'];
        $this->assertFormElementHtml($element);
    }
}
