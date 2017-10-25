<?php

namespace CommonTest\Form\Model\Form\Licence;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

class AddPersonTest extends AbstractFormValidationTestCase
{
    /** @var string The class name of the form being tested */
    protected $formName = \Common\Form\Model\Form\Licence\AddPerson::class;

    public function testContinueToFinancialHistory()
    {
        $element = ['form-actions', 'continueToFinancialHistory'];
        $this->assertFormElementActionButton($element);
    }


    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }

    public function testTitle()
    {
        $element = ['data', 'title'];
        $this->assertFormElementDynamicSelect($element);
    }

    public function testId()
    {
        $element = ['data', 'id'];
        $this->assertFormElementHidden($element);
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
            [\Common\Form\Elements\Validators\DateNotInFuture::IN_FUTURE]
        );
        $this->assertFormElementDate($element);
    }
}
