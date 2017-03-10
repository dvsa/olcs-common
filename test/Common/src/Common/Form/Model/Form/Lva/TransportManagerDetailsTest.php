<?php

namespace CommonTest\Form\Model\Form\Lva;

use Common\Form\Elements\InputFilters\ActionButton;
use Common\Form\Elements\Types\AttachFilesButton;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Form\Element\File;
use Zend\Form\Exception\InvalidArgumentException;
use Zend\Form\Fieldset;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

/**
 * Class TransportManagerDetailsTest
 *
 * @group FormTests
 */
class TransportManagerDetailsTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\TransportManagerDetails::class;

    public function testName()
    {
        $element = ['details', 'name'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testDateOfBirth()
    {
        $element = ['details', 'birthDate'];
        $this->assertFormElementNotValid($element,
            [
                'day' => '15',
                'month' => '06',
                'year' => '2060',
            ],
            [ 'inFuture' ]
        );
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDate($element);
    }

    public function testEmailAddress()
    {
        $element = ['details', 'emailAddress'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementEmailAddress($element);
    }

    public function testPlaceOfBirth()
    {
        $element = [ 'details', 'birthPlace'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementText($element);
    }

    public function testCertificateFileUpload()
    {
        $element = [ 'details', 'certificate', 'file' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementType($element, AttachFilesButton::class);

        $element = [ 'details', 'certificate', '__messages__' ];
        $this->assertFormElementHidden($element);

        $element = [ 'details', 'certificate', 'upload' ];
        $this->assertFormElementType($element, ActionButton::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testHomeAddress()
    {
        $element = [ 'homeAddress', 'id' ];
        $this->assertFormElementRequired($element, false);

        $element = [ 'homeAddress', 'version'];
        $this->assertFormElementRequired($element, false);

        $element = [ 'homeAddress', 'addressLine1' ];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementText($element, 1, 90);

        $element = [ 'homeAddress', 'addressLine2' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementText($element, 1, 90);

        $element = [ 'homeAddress', 'addressLine3' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementText($element, 1, 100);

        $element = [ 'homeAddress', 'addressLine4' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementText($element, 1, 35);

        $element = [ 'homeAddress', 'town' ];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementText($element, 1, 30);

        $element = [ 'homeAddress', 'postcode' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementPostcode($element);

        $element = ['homeAddress', 'countryCode'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);

        $element = ['homeAddress', 'searchPostcode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    /**
     * Even though we use the same Fieldset, we come to
     * an agreement that we will test on a per-form basis
     * as part of a roadmap to centralise/simplify
     * zend forms.
     */
    public function testWorkAddress()
    {
        $element = [ 'workAddress', 'id' ];
        $this->assertFormElementRequired($element, false);

        $element = [ 'workAddress', 'version'];
        $this->assertFormElementRequired($element, false);

        $element = [ 'workAddress', 'addressLine1' ];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementText($element, 1, 90);

        $element = [ 'workAddress', 'addressLine2' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementText($element, 1, 90);

        $element = [ 'workAddress', 'addressLine3' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementText($element, 1, 100);

        $element = [ 'workAddress', 'addressLine4' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementText($element, 1, 35);

        $element = [ 'workAddress', 'town' ];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementText($element, 1, 30);

        $element = [ 'workAddress', 'postcode' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementPostcode($element);

        $element = ['workAddress', 'countryCode'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);

        $element = ['workAddress', 'searchPostcode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementPostcodeSearch($element);
    }

    public function testResponsibilityHoursOfWeek()
    {
        $element = [ 'responsibilities', 'hoursOfWeek', 'hoursPerWeekContent', 'hoursMon' ];
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementNotValid($element, 'abc', [ 'belowMin', 'notFloat', ]);
        $this->assertFormElementValid($element, 1.1);

        $element = [ 'responsibilities', 'hoursOfWeek', 'hoursPerWeekContent', 'hoursTue' ];
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementNotValid($element, 'abc', [ 'notFloat', ]);
        $this->assertFormElementValid($element, 1.1);

        $element = [ 'responsibilities', 'hoursOfWeek', 'hoursPerWeekContent', 'hoursWed' ];
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementNotValid($element, 'abc', [ 'notFloat', ]);
        $this->assertFormElementValid($element, 1.1);

        $element = [ 'responsibilities', 'hoursOfWeek', 'hoursPerWeekContent', 'hoursThu' ];
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementNotValid($element, 'abc', [ 'notFloat', ]);
        $this->assertFormElementValid($element, 1.1);

        $element = [ 'responsibilities', 'hoursOfWeek', 'hoursPerWeekContent', 'hoursFri' ];
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementNotValid($element, 'abc', [ 'notFloat', ]);
        $this->assertFormElementValid($element, 1.1);

        $element = [ 'responsibilities', 'hoursOfWeek', 'hoursPerWeekContent', 'hoursSat' ];
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementNotValid($element, 'abc', [ 'notFloat', ]);
        $this->assertFormElementValid($element, 1.1);

        $element = [ 'responsibilities', 'hoursOfWeek', 'hoursPerWeekContent', 'hoursSun' ];
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementNotValid($element, 'abc', [ 'notFloat', ]);
        $this->assertFormElementValid($element, 1.1);
    }

    public function testLicenceFileUpload()
    {
        $element = [ 'responsibilities', 'file', 'file' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementType($element, AttachFilesButton::class);

        $element = [ 'responsibilities', 'file', '__messages__' ];
        $this->assertFormElementHidden($element);

        $element = [ 'responsibilities', 'file', 'upload' ];
        $this->assertFormElementType($element, ActionButton::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testOtherLicencesTable()
    {
        $element = [ 'responsibilities', 'otherLicences', 'table' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);

        $element = [ 'responsibilities', 'otherLicences', 'action' ];
        $this->assertFormElementHidden($element);

        $element = [ 'responsibilities', 'otherLicences', 'id' ];
        $this->assertFormElementHidden($element);

        $element = [ 'responsibilities', 'otherLicences', 'rows' ];
        $this->assertFormElementHidden($element);
    }

    public function testResponsibilities()
    {
        $element = [ 'responsibilities', 'id' ];
        $this->assertFormElementRequired($element, false);

        $element = [ 'responsibilities', 'version' ];
        $this->assertFormElementRequired($element, false);
    }

    public function testOperatingCentres()
    {
        $element = [ 'responsibilities', 'operatingCentres' ];
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testTradeManagerType()
    {
        $element = [ 'responsibilities', 'tmType' ];
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testIsOwner()
    {
        $element = [ 'responsibilities', 'isOwner' ];
        $this->assertFormElementRequired($element, true);
    }

    public function testTradeManagerApplicationType()
    {
        $element = [ 'responsibilities', 'tmApplicationStatus' ];
        $this->assertFormElementDynamicSelect($element, true);
    }

    public function testAdditionalInformation()
    {
        $element = [ 'responsibilities', 'additionalInformation' ];
        $this->assertFormElementText($element, 0, 4000);
    }

    public function testOtherEmployment()
    {
        $element = [ 'otherEmployment', 'table' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testOtherEmploymentAction()
    {
        $element = [ 'otherEmployment', 'action' ];
        $this->assertFormElementHidden($element);
    }

    public function testOtherEmploymentRows()
    {
        $element = [ 'otherEmployment', 'rows' ];
        $this->assertFormElementHidden($element);
    }

    public function testOtherEmploymentId()
    {
        $element = [ 'otherEmployment', 'id' ];
        $this->assertFormElementHidden($element);
    }

    public function testPreviousHistoryConvictionsTable()
    {
        $element = [ 'previousHistory', 'convictions', 'table' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);

        $element = [ 'previousHistory', 'convictions', 'action' ];
        $this->assertFormElementHidden($element);

        $element = [ 'previousHistory', 'convictions', 'id' ];
        $this->assertFormElementHidden($element);

        $element = [ 'previousHistory', 'convictions', 'rows' ];
        $this->assertFormElementHidden($element);
    }

    public function testPreviousLicencesTable()
    {
        $element = [ 'previousHistory', 'previousLicences', 'table' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);

        $element = [ 'previousHistory', 'previousLicences', 'action' ];
        $this->assertFormElementHidden($element);

        $element = [ 'previousHistory', 'previousLicences', 'id' ];
        $this->assertFormElementHidden($element);

        $element = [ 'previousHistory', 'previousLicences', 'rows' ];
        $this->assertFormElementHidden($element);
    }

    public function testSubmit()
    {
        $element = ['form-actions', 'submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testSave()
    {
        $element = ['form-actions', 'save'];
        $this->assertFormElementActionButton($element);
    }
}
