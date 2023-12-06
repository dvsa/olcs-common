<?php

namespace CommonTest\Common\Form\Model\Form\Licence\Surrender;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

class AddressesTest extends AbstractFormValidationTestCase
{
    protected $formName = \Common\Form\Model\Form\Licence\Surrender\Addresses::class;

    public function testCorrespondenceId()
    {
        $element = ['correspondence', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testCorrespondenceVersion()
    {
        $element = ['correspondence', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testCorrespondenceFao()
    {
        $element = ['correspondence', 'fao'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testCorrespondenceAddressId()
    {
        $element = ['correspondence_address', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testCorrespondenceAddressVersion()
    {
        $element = ['correspondence_address', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testCorrespondenceAddressSearchPostcode()
    {
        $element = ['correspondence_address', 'searchPostcode'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testCorrespondenceAddressAddressLine1()
    {
        $element = ['correspondence_address', 'addressLine1'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testCorrespondenceAddressAddressLine2()
    {
        $element = ['correspondence_address', 'addressLine2'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testCorrespondenceAddressAddressLine3()
    {
        $element = ['correspondence_address', 'addressLine3'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 100);
    }

    public function testCorrespondenceAddressAddressLine4()
    {
        $element = ['correspondence_address', 'addressLine4'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 35);
    }

    public function testCorrespondenceAddressTown()
    {
        $element = ['correspondence_address', 'town'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 30);
    }

    public function testCorrespondenceAddressPostcode()
    {
        $element = ['correspondence_address', 'postcode'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testCorrespondenceAddressCountryCode()
    {
        $element = ['correspondence_address', 'countryCode'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testContactPhonePrimary()
    {
        $element = ['contact', 'phone_primary'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementPhone($element);
    }

    public function testContactPhonePrimaryId()
    {
        $element = ['contact', 'phone_primary_id'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhonePrimaryVersion()
    {
        $element = ['contact', 'phone_primary_version'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneSecondary()
    {
        $element = ['contact', 'phone_secondary'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testContactPhoneSecondaryId()
    {
        $element = ['contact', 'phone_secondary_id'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneSecondaryVersion()
    {
        $element = ['contact', 'phone_secondary_version'];
        $this->assertFormElementHidden($element);
    }

    public function testContactEmail()
    {
        $element = ['contact', 'email'];
        $this->assertFormElementEmailAddress($element);
    }

    public function testSave()
    {
        $element = ['form-actions', 'save'];
        $this->assertFormElementActionButton($element);
    }
}
