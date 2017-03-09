<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Form\Exception\InvalidArgumentException;

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
    }
}
