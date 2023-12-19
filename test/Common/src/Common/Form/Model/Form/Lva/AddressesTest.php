<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class AddressesTest
 *
 * @group FormTests
 */
class AddressesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\Addresses::class;

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

    public function testEstablishmentId()
    {
        $element = ['establishment', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testEstablishmentVersion()
    {
        $element = ['establishment', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testEstablishmentAddressId()
    {
        $element = ['establishment_address', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testEstablishmentAddressVersion()
    {
        $element = ['establishment_address', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testEstablishmentAddressSearchPostcode()
    {
        $element = ['establishment_address', 'searchPostcode'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testEstablishmentAddressAddressLine1()
    {
        $element = ['establishment_address', 'addressLine1'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressAddressLine2()
    {
        $element = ['establishment_address', 'addressLine2'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressAddressLine3()
    {
        $element = ['establishment_address', 'addressLine3'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressAddressLine4()
    {
        $element = ['establishment_address', 'addressLine4'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressTown()
    {
        $element = ['establishment_address', 'town'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressPostcode()
    {
        $element = ['establishment_address', 'postcode'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testEstablishmentAddressCountryCode()
    {
        $element = ['establishment_address', 'countryCode'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testConsultantAddTransportConsultant()
    {
        $element = ['consultant', 'add-transport-consultant'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 'N');
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'X', \Laminas\Validator\InArray::NOT_IN_ARRAY);
    }

    public function testConsultantWrittenPermissionToEngage()
    {
        $element = ['consultant', 'writtenPermissionToEngage'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementCheckbox($element);
    }

    public function testConsultantTransportConsultantName()
    {
        $element = ['consultant', 'transportConsultantName'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element);
    }

    public function testConsultantAddressId()
    {
        $element = ['consultantAddress', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantAddressVersion()
    {
        $element = ['consultantAddress', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantAddressSearchPostcode()
    {
        $element = ['consultantAddress', 'searchPostcode'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testConsultantAddressAddressLine1()
    {
        $element = ['consultantAddress', 'addressLine1'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testConsultantAddressAddressLine2()
    {
        $element = ['consultantAddress', 'addressLine2'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testConsultantAddressAddressLine3()
    {
        $element = ['consultantAddress', 'addressLine3'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 100);
    }

    public function testConsultantAddressAddressLine4()
    {
        $element = ['consultantAddress', 'addressLine4'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 35);
    }

    public function testConsultantAddressTown()
    {
        $element = ['consultantAddress', 'town'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 30);
    }

    public function testConsultantAddressPostcode()
    {
        $element = ['consultantAddress', 'postcode'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testConsultantAddressCountryCode()
    {
        $element = ['consultantAddress', 'countryCode'];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testConsultantContactPhonePrimary()
    {
        $element = ['consultantContact', 'phone_primary'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testConsultantContactPhonePrimaryId()
    {
        $element = ['consultantContact', 'phone_primary_id'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhonePrimaryVersion()
    {
        $element = ['consultantContact', 'phone_primary_version'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneSecondary()
    {
        $element = ['consultantContact', 'phone_secondary'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testConsultantContactPhoneSecondaryId()
    {
        $element = ['consultantContact', 'phone_secondary_id'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneSecondaryVersion()
    {
        $element = ['consultantContact', 'phone_secondary_version'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactEmail()
    {
        $element = ['consultantContact', 'email'];
        $this->assertFormElementEmailAddress($element);
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
