<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class TaxiPhvLicenceTest
 *
 * @group FormTests
 */
class TaxiPhvLicenceTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\TaxiPhvLicence::class;

    public function testTrafficArea()
    {
        $element = ['trafficArea'];
        $this->assertFormElementHidden($element);
    }

    public function testVersion()
    {
        $element = ['data', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testPrivateHireLicenceNo()
    {
        $element = ['data', 'privateHireLicenceNo'];
        $this->assertFormElementText($element);
    }

    public function testContactDetailsId()
    {
        $element = ['contactDetails', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testContactDetailsVersion()
    {
        $element = ['contactDetails', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testContactDetailsDescription()
    {
        $element = ['contactDetails', 'description'];
        $this->assertFormElementText($element);
        $this->assertFormElementIsRequired($element, true);
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

    public function testAddressSearchPostcode()
    {
        $element = ['address', 'searchPostcode'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testAddressAddressLine1()
    {
        $element = ['address', 'addressLine1'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testAddressAddressLine2()
    {
        $element = ['address', 'addressLine2'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testAddressAddressLine3()
    {
        $element = ['address', 'addressLine3'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 100);
    }

    public function testAddressAddressLine4()
    {
        $element = ['address', 'addressLine4'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 35);
    }

    public function testAddressTown()
    {
        $element = ['address', 'town'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 30);
    }

    public function testAddressPostcode()
    {
        $element = ['address', 'postcode'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testAddressCountryCode()
    {
        $element = ['address', 'countryCode'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);
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
