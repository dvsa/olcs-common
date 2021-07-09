<?php

namespace CommonTest\Form\Model\Form\Lva;

use Common\Validator\Date;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Elements\InputFilters\SingleCheckbox;

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

    public function testDataHtml()
    {
        $element = ['data', 'dataHtml'];
        $this->assertFormElementHtml($element);
    }

    public function testHgvHtml()
    {
        $element = ['data', 'hgvHtml'];
        $this->assertFormElementHtml($element);
    }

    public function testNumberOfHgvVehiclesRequired()
    {
        $element = ['data', 'noOfHgvVehiclesRequired'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 0);
        $this->assertFormElementValid($element, 1000000);
    }

    public function testLgvHtml()
    {
        $element = ['data', 'lgvHtml'];
        $this->assertFormElementHtml($element);
    }

    public function testNumberOfLgvVehiclesRequired()
    {
        $element = ['data', 'noOfLgvVehiclesRequired'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 0);
        $this->assertFormElementValid($element, 1000000);
    }

    public function testTrailersHtml()
    {
        $element = ['data', 'trailersHtml'];
        $this->assertFormElementHtml($element);
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
        $this->assertFormElementType($element, \Laminas\Form\Element\Radio::class);
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
                \Laminas\Validator\Date::INVALID_DATE,
            ]
        );
    }

    public function testAdvertisementsAdSendByPostContent()
    {
        $element = ['advertisements', 'adSendByPostContent'];
        $this->assertFormElementHtml($element);
    }

    public function testAdvertisementsAdPlacedLaterContent()
    {
        $element = ['advertisements', 'adPlacedLaterContent'];
        $this->assertFormElementHtml($element);
    }

    public function testAdvertisementsMultiFileUploadControls()
    {
        $element = ['advertisements', 'adPlacedContent', 'file'];
        $this->assertFormElementMultipleFileUpload($element);
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
