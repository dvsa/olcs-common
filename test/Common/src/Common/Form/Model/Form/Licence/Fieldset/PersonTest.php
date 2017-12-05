<?php
namespace CommonTest\Form\Model\Form\Licence\Fieldset;

use Common\Form\Elements\Validators\DateNotInFuture;
use Common\Form\Model\Form\Licence\Fieldset\Person;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

class PersonTest extends AbstractFormValidationTestCase
{
    /** @var string The class name of the form being tested */
    protected $formName = Person::class;

    public function testId()
    {
        $element = ['id'];
        $this->assertFormElementHidden($element);
    }

    public function testTitle()
    {
        $element = ['title'];
        $this->assertFormElementDynamicSelect($element);
    }

    public function testForename()
    {
        $element = ['forename'];
        $this->assertFormElementText($element, 1, 35);
    }

    public function testFamilyName()
    {
        $element = ['familyName'];
        $this->assertFormElementText($element, 1, 35);
    }

    public function testOtherName()
    {
        $element = ['otherName'];
        $this->assertFormElementText($element);
    }

    public function testDateOfBirth()
    {
        $element = ['birthDate'];
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
