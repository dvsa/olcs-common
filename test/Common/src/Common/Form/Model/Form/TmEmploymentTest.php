<?php

namespace CommonTest\Form\Model\Form;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class TmEmploymentTest
 *
 * @group FormTests
 */
class TmEmploymentTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\TmEmployment::class;

    public function testEmployerName()
    {
        $element = ['tm-employer-name-details', 'employerName'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testAddressId()
    {
        $element = ['address', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testAddressVersion()
    {
        $element = ['address', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testSearchPostcode()
    {
        $element = ['address', 'searchPostcode'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testAddressLine1()
    {
        $element = ['address', 'addressLine1'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testAddressLine2()
    {
        $element = ['address', 'addressLine2'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testAddressLine3()
    {
        $element = ['address', 'addressLine3'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 100);
    }

    public function testAddressLine4()
    {
        $element = ['address', 'addressLine4'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 35);
    }

    public function testTown()
    {
        $element = ['address', 'town'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 30);
    }

    public function testPostcode()
    {
        $element = ['address', 'postcode'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testCountryCode()
    {
        $element = ['address', 'countryCode'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testTmEmploymentDetailsId()
    {
        $element = ['tm-employment-details', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testTmEmploymentDetailsVersion()
    {
        $element = ['tm-employment-details', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testTmEmploymentDetailsPosition()
    {
        $element = ['tm-employment-details', 'position'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 45);
    }

    public function testTmEmploymentDetailsHoursPerWeek()
    {
        $element = ['tm-employment-details', 'hoursPerWeek'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 300);
    }

    public function testSubmit()
    {
        $element = ['form-actions', 'submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }

    public function testAddAnother()
    {
        $element = ['form-actions', 'addAnother'];
        $this->assertFormElementActionButton($element);
    }
}
