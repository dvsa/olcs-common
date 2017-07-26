<?php

namespace CommonTest\Form\Model\Form\Continuation;

use Dvsa\Olcs\Transfer\Validators\Money;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Validator\Between;
use Zend\Validator\GreaterThan;
use Zend\Validator\LessThan;
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

    public function testFinancesFactoring()
    {
        $element = ['finances', 'factoring', 'yesNo'];
        $this->assertFormElementIsRequired($element, true, [0 => 0]);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
        $this->assertFormElementNotValid($element, 'X', 'continuations.finances.factoring.error');
        $this->assertFormElementNotValid($element, '', 'continuations.finances.factoring.error');
    }

    public function testFinancesOtherFinancesAmount()
    {
        $element = ['finances', 'factoring', 'yesContent', 'amount'];

        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);

        $_POST = ['finances' => ['factoring' => ['yesNo' => 'Y']]];
        $this->assertFormElementAllowEmpty($element, false);

        $this->assertFormElementNotValid($element, 'X99999999', Money::INVALID);
        $this->assertFormElementNotValid($element, '999999991', LessThan::NOT_LESS_INCLUSIVE);
        $this->assertFormElementNotValid($element, '-1', GreaterThan::NOT_GREATER);
        $this->assertFormElementValid($element, '99999999');
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
