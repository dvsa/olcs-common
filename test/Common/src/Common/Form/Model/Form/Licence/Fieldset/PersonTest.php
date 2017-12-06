<?php

namespace CommonTest\Form\Model\Form\Licence\Fieldset;

use Common\Form\Elements\Validators\DateNotInFuture;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

class PersonTest extends AbstractFormValidationTestCase
{
    protected $formName = PersonContainerTestStub::class;

    public function testId()
    {
        $element = ['data', 'id'];
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
