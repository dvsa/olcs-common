<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\I18n\Validator\IsFloat;

/**
 * Class TmOtherLicenceTest
 *
 * @group FormTests
 */
class TmOtherLicenceTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\TmOtherLicence::class;

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

    public function testRedirectAction()
    {
        $element = ['data', 'redirectAction'];
        $this->assertFormElementHidden($element);
    }

    public function testRedirectId()
    {
        $element = ['data', 'redirectId'];
        $this->assertFormElementHidden($element);
    }

    public function testLicenceNo()
    {
        $element = ['data', 'licNo'];
        $this->assertFormElementText($element, 1, 18);
        $this->assertFormElementRequired($element, true);
    }

    public function testRole()
    {
        $element = ['data', 'role'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementDynamicSelect($element, false);
    }

    public function testTotalAuthVehicles()
    {
        $element = ['data', 'totalAuthVehicles'];
        $this->assertFormElementNumber($element);
    }

    public function testTotalHoursPerWeek()
    {
        $element = ['data', 'hoursPerWeek'];
        $this->assertFormElementValid($element, 99.9);
        $this->assertFormElementValid($element, 1);
        $this->assertFormElementValid($element, 0);
        $this->assertFormElementNotValid($element, 'abc', [IsFloat::NOT_FLOAT]);
    }

    public function testOperatingCentres()
    {
        $element = ['data', 'operatingCentres'];
        $this->assertFormElementAllowEmpty($element, false);
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
