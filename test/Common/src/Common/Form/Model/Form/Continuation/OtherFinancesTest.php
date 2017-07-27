<?php

namespace CommonTest\Form\Model\Form\Continuation;

use Dvsa\Olcs\Transfer\Validators\Money;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Validator\GreaterThan;
use Zend\Validator\LessThan;

/**
 * Class OtherFinancesTest
 *
 * @group FormTests
 */
class OtherFinancesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Continuation\OtherFinances::class;

    public function testFinancesOtherFinances()
    {
        $element = ['finances', 'yesNo'];
        $this->assertFormElementIsRequired($element, true, [0 => 0]);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
        $this->assertFormElementNotValid($element, 'X', 'continuations.finances.otherFinances.error');
        $this->assertFormElementNotValid($element, '', 'continuations.finances.otherFinances.error');
    }

    public function testFinancesOtherFinancesAmount()
    {
        $element = ['finances', 'yesContent', 'amount'];

        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);

        $_POST = ['finances' => ['otherFinances' => ['yesNo' => 'Y']]];
        $this->assertFormElementAllowEmpty($element, false);

        $this->assertFormElementNotValid($element, 'X99999999', Money::INVALID);
        $this->assertFormElementNotValid($element, '10000000000', LessThan::NOT_LESS);
        $this->assertFormElementNotValid($element, '-1', GreaterThan::NOT_GREATER);
        $this->assertFormElementNotValid($element, '0', GreaterThan::NOT_GREATER);
        $this->assertFormElementValid($element, '1');
        $this->assertFormElementValid($element, '9999999999');
    }

    public function testFinancesOtherFinancesDetail()
    {
        $element = ['finances', 'yesContent', 'detail'];

        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);

        $_POST = ['finances' => ['otherFinances' => ['yesNo' => 'Y']]];
        $this->assertFormElementAllowEmpty($element, false);

        $this->assertFormElementText($element, 0, 200);
    }

    public function testSubmit()
    {
        $element = ['submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testVersion()
    {
        $element = ['version'];
        $this->assertFormElementHidden($element);
    }
}
