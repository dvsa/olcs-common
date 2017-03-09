<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Form\Exception\InvalidArgumentException;
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

    public function testOtherLicences()
    {
        $element = [ 'responsibilities', 'otherLicences' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testResponsibilityHoursOfWeek()
    {
        $element = ['responsibilities', 'hoursOfWeek'];

        $this->assertFormElementNotValid($element,
            [
                'hoursMon' => null,
                'hoursTue' => null,
                'hoursWed' => null,
                'hoursThu' => null,
                'hoursFri' => null,
                'hoursSat' => null,
                'hoursSun' => null,
            ],
            [ 'hoursPerWeekContent' ]
        );

        $this->assertFormElementNotValid($element,
            [
                'hoursMon' => 0,
                'hoursTue' => 0,
                'hoursWed' => 0,
                'hoursThu' => 0,
                'hoursFri' => 0,
                'hoursSat' => 0,
                'hoursSun' => 0,
            ],
            [ 'hoursPerWeekContent' ]
        );

        $this->assertFormElementValid($element,
            [
                'hoursMon' => 1,
                'hoursTue' => null,
                'hoursWed' => null,
                'hoursThu' => null,
                'hoursFri' => null,
                'hoursSat' => null,
                'hoursSun' => null,
            ]
        );

        $this->assertFormElementValid($element,
            [
                'hoursMon' => 1,
                'hoursTue' => 0,
                'hoursWed' => 0,
                'hoursThu' => 0,
                'hoursFri' => 0,
                'hoursSat' => 0,
                'hoursSun' => 0,
            ]
        );

        $this->assertFormElementValid($element,
            [
                'hoursMon' => 10,
                'hoursTue' => 10,
                'hoursWed' => 10,
                'hoursThu' => 10,
                'hoursFri' => 10,
                'hoursSat' => 10,
                'hoursSun' => 10,
            ]
        );
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
        $this->assertFormElementDynamicSelect($element, false);
    }

    public function testTradeManagerType()
    {
        $element = [ 'responsibilities', 'tmType' ];
        $this->assertFormElementDynamicRadio($element);
    }

    public function testIsOwner()
    {
        $element = [ 'responsibilities', 'isOwner' ];
        $this->assertFormElementDynamicRadio($element);
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

    public function testPreviousHistory()
    {
        $element = [ 'previousHistory', 'convictions' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);

        $element = [ 'previousHistory', 'previousLicences' ];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
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
