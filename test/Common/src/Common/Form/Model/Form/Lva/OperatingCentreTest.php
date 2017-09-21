<?php

namespace CommonTest\Form\Model\Form\Lva;

use Common\Form\Elements\Types\FileUploadList;
use Common\Validator\Date;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Elements\Types\AttachFilesButton;
use Common\Form\Elements\InputFilters\ActionButton;
use Common\Form\Elements\InputFilters\SingleCheckbox;
use Common\Validator\OneOf;
use Zend\Validator\NotEmpty;
use Zend\Form\Element\Hidden;

/**
 * Class OperatingCentreTest
 *
 * @group FormTests
 */
class OperatingCentreTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\OperatingCentre::class;

    public function testVersion()
    {
        $element = [ 'version' ];
        $this->assertFormElementHidden($element);
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

    public function testCorrespondenceAddressAddressLine2()
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

    public function testNumberOfVehiclesRequired()
    {
        $element = [ 'data', 'noOfVehiclesRequired' ];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 0);
        $this->assertFormElementValid($element, 1000000);
    }

    public function testNumberOfTrailersRequired()
    {
        $element = [ 'data', 'noOfTrailersRequired' ];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 0);
        $this->assertFormElementValid($element, 1000000);
    }

    public function testPermissionCheckbox()
    {
        $element = [ 'data', 'permission', 'permission' ];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementIsRequired($element, true);
    }

    public function testAdvertisementsAdPlaced()
    {
        $element = [ 'advertisements', 'radio' ];
        $this->assertFormElementType($element, \Zend\Form\Element\Radio::class);
        $this->assertFormElementIsRequired($element, true, [0 => 0]);
        $this->assertFormElementValid($element, 'adPlaced');
        $this->assertFormElementValid($element, 'adSendByPost');
        $this->assertFormElementValid($element, 'adPlacedLater');
        $this->assertFormElementNotValid($element, '', 'advertisements_adPlaced-error');
    }

    public function testAdvertisementsAdIn()
    {
        $element = [ 'advertisements', 'adPlacedContent', 'adPlacedIn' ];
        $this->assertFormElementText($element);
        $this->assertFormElementIsRequired($element, false);
    }

    public function testAdvertisementsPlacedDate()
    {
        $element = [ 'advertisements', 'adPlacedContent', 'adPlacedDate' ];
        $this->assertFormElementDate($element);
        $this->assertFormElementIsRequired(
            $element,
            true,
            [
                Date::DATE_ERR_CONTAINS_STRING,
                Date::DATE_ERR_YEAR_LENGTH,
                \Zend\Validator\Date::INVALID_DATE,
            ]
        );
    }

    public function testAdvertisementsMultiFileUploadControls()
    {
        $element = [ 'advertisements', 'adPlacedContent', 'file', 'file' ];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementType($element, AttachFilesButton::class);

        $element = [ 'advertisements', 'adPlacedContent', 'file', '__messages__' ];
        $this->assertFormElementHidden($element);

        $element = [ 'advertisements', 'adPlacedContent', 'file', 'list' ];
        $this->assertFormElementType($element, FileUploadList::class);

        $element = [ 'advertisements', 'adPlacedContent', 'file', 'upload' ];
        $this->assertFormElementType($element, ActionButton::class);
        $this->assertFormElementIsRequired($element, false);
    }

    public function testAdvertisementsFileUploadCount()
    {
        $element = [ 'advertisements', 'uploadedFileCount' ];
        $this->assertFormElementHidden($element);
        $this->assertFormElementIsRequired($element, false);
    }

    public function testSubmit()
    {
        $element = ['form-actions', 'submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testAddAnother()
    {
        $element = ['form-actions', 'addAnother'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
