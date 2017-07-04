<?php

namespace CommonTest\Form\Model\Form\Continuation;

use Dvsa\Olcs\Transfer\Validators\Money;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Validator\Between;
use Zend\Validator\NotEmpty;

/**
 * Class FinancesTest
 *
 * @group FormTests
 */
class FinancesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Continuation\Finances::class;

    public function testFinancesAverageBalance()
    {
        $element = ['finances', 'averageBalance'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementIsRequired($element, true);

        $this->assertFormElementNotValid($element, 'X99999999', Money::INVALID);
        $this->assertFormElementNotValid($element, '999999991', Between::NOT_BETWEEN);
        $this->assertFormElementNotValid($element, '-999999991', Between::NOT_BETWEEN);
        $this->assertFormElementValid($element, '-99999999');
        $this->assertFormElementValid($element, '99999999');
    }

    public function testFinancesOverdraftFacility()
    {
        $element = ['finances', 'overdraftFacility', 'yesNo'];
        $this->assertFormElementIsRequired($element, true, [0 => 0]);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
        $this->assertFormElementNotValid($element, 'X', 'continuations.finances.overdraftFacility.error');
        $this->assertFormElementNotValid($element, '', 'continuations.finances.overdraftFacility.error');
    }

    public function testFinancesOverdraftFacilityYesContent()
    {
        $yesContext = ['finances' => ['overdraftFacility' => ['yesNo' => 'Y']]];

        $element = ['finances', 'overdraftFacility', 'yesContent'];
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementAllowEmpty($element, false, $yesContext);
        $this->assertFormElementIsRequired($element, true);

        $this->assertFormElementNotValid($element, 'X99999999', Money::INVALID, $yesContext);
        $this->assertFormElementNotValid($element, '999999991', Between::NOT_BETWEEN, $yesContext);
        $this->assertFormElementNotValid($element, '-1', Money::INVALID, $yesContext);
        $this->assertFormElementValid($element, '99999999', $yesContext);
    }

    public function testFinancesOtherFinances()
    {
        $element = ['finances', 'otherFinances', 'yesNo'];
        $this->assertFormElementIsRequired($element, true, [0 => 0]);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
        $this->assertFormElementNotValid($element, 'X', 'continuations.finances.otherFinances.error');
        $this->assertFormElementNotValid($element, '', 'continuations.finances.otherFinances.error');
    }

    public function testFinancesOtherFinancesAmount()
    {
        $element = ['finances', 'otherFinances', 'yesContent', 'amount'];

        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);

        $_POST = ['finances' => ['otherFinances' => ['yesNo' => 'Y']]];
        $this->assertFormElementAllowEmpty($element, false);

        $this->assertFormElementNotValid($element, 'X99999999', Money::INVALID);
        $this->assertFormElementNotValid($element, '999999991', Between::NOT_BETWEEN);
        $this->assertFormElementNotValid($element, '-1', Money::INVALID);
        $this->assertFormElementValid($element, '99999999');
    }

    public function testFinancesOtherFinancesDetail()
    {
        $element = ['finances', 'otherFinances', 'yesContent', 'detail'];

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
        $element = ['finances', 'version'];
        $this->assertFormElementHidden($element);
    }
}
