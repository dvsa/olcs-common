<?php

namespace CommonTest\Form\Model\Form\Lva;

use Common\Form\Elements\Types\FileUploadList;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Elements\Types\AttachFilesButton;
use Common\Form\Elements\InputFilters\ActionButton;
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
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testAddressAddressLine1()
    {
        $element = ['address', 'addressLine1'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testCorrespondenceAddressAddressLine2()
    {
        $element = ['address', 'addressLine2'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 90);
    }

    public function testAddressAddressLine3()
    {
        $element = ['address', 'addressLine3'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 100);
    }

    public function testAddressAddressLine4()
    {
        $element = ['address', 'addressLine4'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 0, 35);
    }

    public function testAddressTown()
    {
        $element = ['address', 'town'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 0, 30);
    }

    public function testAddressPostcode()
    {
        $element = ['address', 'postcode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcode($element);
    }

    public function testAddressCountryCode()
    {
        $element = ['address', 'countryCode'];
        $this->assertFormElementRequired($element, true);
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

    public function testSufficientParkingCheckbox()
    {
        $element = [ 'data', 'sufficientParking' ];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testPermissionCheckbox()
    {
        $element = [ 'data', 'permission' ];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testAdvertisementsAdPlaced()
    {
        $element = [ 'advertisements', 'adPlaced' ];
        $this->assertFormElementType($element, \Zend\Form\Element\Radio::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testAdvertisementsAdIn()
    {
        $element = [ 'advertisements', 'adPlacedIn' ];
        $this->assertFormElementText($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testAdvertisementsPlacedDate()
    {
        $element = [ 'advertisements', 'adPlacedDate' ];
        $this->assertFormElementDate($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testAdvertisementsMultiFileUploadControls()
    {
        $element = [ 'advertisements', 'file', 'file' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementType($element, AttachFilesButton::class);

        $element = [ 'advertisements', 'file', '__messages__' ];
        $this->assertFormElementHidden($element);

        $element = [ 'advertisements', 'file', 'list' ];
        $this->assertFormElementType($element, FileUploadList::class);
        $this->assertFormElementRequired($element, false);

        $element = [ 'advertisements', 'file', 'upload' ];
        $this->assertFormElementType($element, ActionButton::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testAdvertisementsFileUploadCount()
    {
        $element = [ 'advertisements', 'uploadedFileCount' ];
        $this->assertFormElementHidden($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testAdvertisementAdSendByPost()
    {
        $element = [ 'advertisements', 'adSendByPost' ];
        $this->assertFormElementHtml($element);
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
