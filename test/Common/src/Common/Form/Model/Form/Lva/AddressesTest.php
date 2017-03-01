<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

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
        $this->assertFormElementRequired($element, false);
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
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testCorrespondenceAddressAddressLine1()
    {
        $element = ['correspondence_address', 'addressLine1'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testCorrespondenceAddressAddressLine2()
    {
        $element = ['correspondence_address', 'addressLine2'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testCorrespondenceAddressAddressLine3()
    {
        $element = ['correspondence_address', 'addressLine3'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 100);
    }

    public function testCorrespondenceAddressAddressLine4()
    {
        $element = ['correspondence_address', 'addressLine4'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 35);
    }

    public function testCorrespondenceAddressTown()
    {
        $element = ['correspondence_address', 'town'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 30);
    }

    public function testCorrespondenceAddressPostcode()
    {
        $element = ['correspondence_address', 'postcode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testCorrespondenceAddressCountryCode()
    {
        $element = ['correspondence_address', 'countryCode'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testPhoneContactsTableTable()
    {
        $element = ['phoneContactsTable', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testPhoneContactsTableAction()
    {
        $element = ['phoneContactsTable', 'action'];
        $this->assertFormElementNoRender($element);
    }

    public function testPhoneContactsTableRows()
    {
        $element = ['phoneContactsTable', 'rows'];
        $this->assertFormElementHidden($element);
    }

    public function testPhoneContactsTableId()
    {
        $element = ['phoneContactsTable', 'id'];
        $this->assertFormElementNoRender($element);
    }

    public function testContactPhoneValidator()
    {
        $element = ['contact', 'phone-validator'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementValid($element, '', ['contact' => ['phone_business' => '0123456789']]);
        $this->assertFormElementValid($element, '', ['contact' => ['phone_home' => '0123456789']]);
        $this->assertFormElementValid($element, '', ['contact' => ['phone_mobile' => '0123456789']]);
        $this->assertFormElementValid($element, '', ['contact' => ['phone_fax' => '0123456789']]);
        $this->assertFormElementNotValid($element, '1', \Common\Validator\OneOf::PROVIDE_ONE);
    }

    public function testContactPhoneBusiness()
    {
        $element = ['contact', 'phone_business'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testContactPhoneBusinessId()
    {
        $element = ['contact', 'phone_business_id'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneBusinessVersion()
    {
        $element = ['contact', 'phone_business_version'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneHome()
    {
        $element = ['contact', 'phone_home'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testContactPhoneHomeId()
    {
        $element = ['contact', 'phone_home_id'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneHomeVersion()
    {
        $element = ['contact', 'phone_home_version'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneMobile()
    {
        $element = ['contact', 'phone_mobile'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testContactPhoneMobileId()
    {
        $element = ['contact', 'phone_mobile_id'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneMobileVersion()
    {
        $element = ['contact', 'phone_mobile_version'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneFax()
    {
        $element = ['contact', 'phone_fax'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testContactPhoneFaxId()
    {
        $element = ['contact', 'phone_fax_id'];
        $this->assertFormElementHidden($element);
    }

    public function testContactPhoneFaxVersion()
    {
        $element = ['contact', 'phone_fax_version'];
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
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testEstablishmentAddressAddressLine1()
    {
        $element = ['establishment_address', 'addressLine1'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressAddressLine2()
    {
        $element = ['establishment_address', 'addressLine2'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressAddressLine3()
    {
        $element = ['establishment_address', 'addressLine3'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressAddressLine4()
    {
        $element = ['establishment_address', 'addressLine4'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressTown()
    {
        $element = ['establishment_address', 'town'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testEstablishmentAddressPostcode()
    {
        $element = ['establishment_address', 'postcode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testEstablishmentAddressCountryCode()
    {
        $element = ['establishment_address', 'countryCode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testConsultantAddTransportConsultant()
    {
        $element = ['consultant', 'add-transport-consultant'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 'N');
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'X', \Zend\Validator\InArray::NOT_IN_ARRAY);
    }

    public function testConsultantWrittenPermissionToEngage()
    {
        $element = ['consultant', 'writtenPermissionToEngage'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementCheckbox($element);
    }

    public function testConsultantTransportConsultantName()
    {
        $element = ['consultant', 'transportConsultantName'];
        $this->assertFormElementRequired($element, true);
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
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testConsultantAddressAddressLine1()
    {
        $element = ['consultantAddress', 'addressLine1'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testConsultantAddressAddressLine2()
    {
        $element = ['consultantAddress', 'addressLine2'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testConsultantAddressAddressLine3()
    {
        $element = ['consultantAddress', 'addressLine3'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 100);
    }

    public function testConsultantAddressAddressLine4()
    {
        $element = ['consultantAddress', 'addressLine4'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 35);
    }

    public function testConsultantAddressTown()
    {
        $element = ['consultantAddress', 'town'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 30);
    }

    public function testConsultantAddressPostcode()
    {
        $element = ['consultantAddress', 'postcode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testConsultantAddressCountryCode()
    {
        $element = ['consultantAddress', 'countryCode'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testConsultantContactPhoneBusiness()
    {
        $element = ['consultantContact', 'phone_business'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testConsultantContactPhoneBusinessId()
    {
        $element = ['consultantContact', 'phone_business_id'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneBusinessVersion()
    {
        $element = ['consultantContact', 'phone_business_version'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneHome()
    {
        $element = ['consultantContact', 'phone_home'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testConsultantContactPhoneHomeId()
    {
        $element = ['consultantContact', 'phone_home_id'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneHomeVersion()
    {
        $element = ['consultantContact', 'phone_home_version'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneMobile()
    {
        $element = ['consultantContact', 'phone_mobile'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testConsultantContactPhoneMobileId()
    {
        $element = ['consultantContact', 'phone_mobile_id'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneMobileVersion()
    {
        $element = ['consultantContact', 'phone_mobile_version'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneFax()
    {
        $element = ['consultantContact', 'phone_fax'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPhone($element);
    }

    public function testConsultantContactPhoneFaxId()
    {
        $element = ['consultantContact', 'phone_fax_id'];
        $this->assertFormElementHidden($element);
    }

    public function testConsultantContactPhoneFaxVersion()
    {
        $element = ['consultantContact', 'phone_fax_version'];
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
