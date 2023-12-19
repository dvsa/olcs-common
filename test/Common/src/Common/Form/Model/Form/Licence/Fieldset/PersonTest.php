<?php
namespace CommonTest\Common\Form\Model\Form\Licence\Fieldset;

use Common\Form\Elements\Validators\DateNotInFuture;
use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

class PersonTest extends AbstractFormValidationTestCase
{
    /** @var string The class name of the form being tested */
    // tested against a stub of the form which contains the fieldset to be tested
    // as \Common\Form\Model\Form\Licence\AddPerson uses the fieldset as a collection
    // which is not currently supported by the AbstractFormValidationTestCase
    protected $formName = PersonFormStub::class;

    public function testId()
    {
        $element = ['data', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testVersion()
    {
        $element = ['data', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testTitle()
    {
        $element = ['data', 'title'];
        $this->assertFormElementDynamicSelect($element);
    }

    public function testForename()
    {
        $element = ['data', 'forename'];
        $this->assertFormElementText($element, 1, 35);
    }

    public function testFamilyName()
    {
        $element = ['data', 'familyName'];
        $this->assertFormElementText($element, 1, 35);
    }

    public function testOtherName()
    {
        $element = ['data', 'otherName'];
        $this->assertFormElementText($element);
    }

    public function testDateOfBirth()
    {
        $element = ['data', 'birthDate'];
        $this->assertFormElementNotValid(
            $element,
            [
                'day' => '15',
                'month' => '06',
                'year' => '2060'
            ],
            [DateNotInFuture::IN_FUTURE]
        );

        $this->assertFormElementDate($element);
    }
}
