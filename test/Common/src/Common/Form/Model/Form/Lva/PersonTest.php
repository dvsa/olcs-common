<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class PersonTest
 *
 * @group FormTests
 */
class PersonTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\Person::class;

    public function testTitle()
    {
        $element = [ 'data', 'title' ];
        $this->assertFormElementDynamicSelect($element);
    }

    public function testId()
    {
        $element = [ 'data', 'id' ];
        $this->assertFormElementHidden($element);
    }

    public function testVersion()
    {
        $element = [ 'data', 'version' ];
        $this->assertFormElementHidden($element);
    }

    public function testForename()
    {
        $element = [ 'data', 'forename' ];
        $this->assertFormElementText($element, 1, 35);
    }

    public function testFamilyName()
    {
        $element = [ 'data', 'familyName' ];
        $this->assertFormElementText($element, 1, 35);
    }

    public function testOtherName()
    {
        $element = [ 'data', 'otherName' ];
        $this->assertFormElementText($element);
    }

    public function testPosition()
    {
        $element = [ 'data', 'position' ];
        $this->assertFormElementText($element);
    }

    public function testDateOfBirth()
    {
        $element = ['data', 'birthDate'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementNotValid(
            $element,
            [
                'day' => '15',
                'month' => '06',
                'year' => '2060',
            ],
            [ \Common\Form\Elements\Validators\DateNotInFuture::IN_FUTURE ]
        );
        $this->assertFormElementDate($element);
    }

    public function testSubmit()
    {
        $element = ['form-actions', 'submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }

    public function testDisquality()
    {
        $element = ['form-actions', 'disqualify'];
        $this->assertFormElementActionButton($element);
    }

    public function testAddAnother()
    {
        $element = ['form-actions', 'addAnother'];
        $this->assertFormElementActionButton($element);
    }
}
