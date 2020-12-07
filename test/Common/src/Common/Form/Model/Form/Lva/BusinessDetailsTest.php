<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class BusinessDetailsTest
 *
 * @group FormTests
 */
class BusinessDetailsTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\BusinessDetails::class;

    public function testDataCompanyNumber()
    {
        $element = ['data', 'companyNumber'];
        $this->assertFormElementCompanyNumberType($element);
    }

    public function testDataName()
    {
        $element = ['data', 'name'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 200);
    }

    public function testDataNatureOfBusiness()
    {
        $element = ['data', 'natureOfBusiness'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 200);
    }

    public function testRegisteredAddressId()
    {
        $element = ['registeredAddress', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testRegisteredAddressVersion()
    {
        $element = ['registeredAddress', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testRegisteredAddressAddressLine1()
    {
        $element = ['registeredAddress', 'addressLine1'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 200);
    }

    public function testRegisteredAddressAddressLine2()
    {
        $element = ['registeredAddress', 'addressLine2'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 200);
    }

    public function testRegisteredAddressAddressLine3()
    {
        $element = ['registeredAddress', 'addressLine3'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 200);
    }

    public function testRegisteredAddressAddressLine4()
    {
        $element = ['registeredAddress', 'addressLine4'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 200);
    }

    public function testRegisteredAddressTown()
    {
        $element = ['registeredAddress', 'town'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 200);
    }

    public function testRegisteredAddressPostcode()
    {
        $element = ['registeredAddress', 'postcode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testTableTable()
    {
        $element = ['table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testTableAction()
    {
        $element = ['table', 'action'];
        $this->assertFormElementNoRender($element);
    }

    public function testTableRows()
    {
        $element = ['table', 'rows'];
        $this->assertFormElementHidden($element);
    }

    public function testTableId()
    {
        $element = ['table', 'id'];
        $this->assertFormElementNoRender($element);
    }

    public function testAllowEmailAllowEmail()
    {
        $element = ['allow-email', 'allowEmail'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 'N');
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'X', \Laminas\Validator\InArray::NOT_IN_ARRAY);
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

    public function testVersion()
    {
        $element = ['version'];
        $this->assertFormElementHidden($element);
    }
}
