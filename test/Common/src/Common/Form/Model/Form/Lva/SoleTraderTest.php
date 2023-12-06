<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class SoleTraderTest
 *
 * @group FormTests
 */
class SoleTraderTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\SoleTrader::class;

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

    public function testVersion()
    {
        $element = ['data', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testForename()
    {
        $element = ['data', 'forename'];
        $this->assertFormElementText($element);
    }

    public function testFamilyName()
    {
        $element = ['data', 'familyName'];
        $this->assertFormElementText($element);
    }

    public function testOtherName()
    {
        $element = ['data', 'otherName'];
        $this->assertFormElementText($element);
    }

    public function testDateOfBirth()
    {
        $element = ['data', 'birthDate'];
        $this->assertFormElementValid(
            $element,
            [
                'day'   => '15',
                'month' => '06',
                'year'  => '1987',
            ],
            [\Common\Form\Elements\Validators\DateNotInFuture::IN_FUTURE]
        );
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDate($element);
    }

    public function testSave()
    {
        $element = ['form-actions', 'save'];
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

    public function testSaveAndContinue()
    {
        $element = ['form-actions', 'saveAndContinue'];
        $this->assertFormElementActionButton($element);
    }
}
