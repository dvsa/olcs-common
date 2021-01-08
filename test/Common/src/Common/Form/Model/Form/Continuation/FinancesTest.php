<?php

namespace CommonTest\Form\Model\Form\Continuation;

use Dvsa\Olcs\Transfer\Validators\Money;
use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\Validator\Between;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use Laminas\Validator\NotEmpty;

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
        $this->assertFormElementNotValid($element, '10000000000', LessThan::NOT_LESS);
        $this->assertFormElementNotValid($element, '-10000000000', GreaterThan::NOT_GREATER);
        $this->assertFormElementValid($element, '-9999999999');
        $this->assertFormElementValid($element, '9999999999');
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
        $this->assertFormElementNotValid($element, '10000000000', LessThan::NOT_LESS, $yesContext);
        $this->assertFormElementNotValid($element, '-1', GreaterThan::NOT_GREATER, $yesContext);
        $this->assertFormElementNotValid($element, '0', GreaterThan::NOT_GREATER, $yesContext);
        $this->assertFormElementValid($element, '1', $yesContext);
        $this->assertFormElementValid($element, '9999999999', $yesContext);
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

    public function testFinancesFactoringAmount()
    {
        $element = ['finances', 'factoring', 'yesContent', 'amount'];

        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementAllowEmpty($element, true);

        $_POST = ['finances' => ['factoring' => ['yesNo' => 'Y']]];
        $this->assertFormElementAllowEmpty($element, false);

        $this->assertFormElementNotValid($element, 'X99999999', Money::INVALID);
        $this->assertFormElementNotValid($element, '10000000000', LessThan::NOT_LESS);
        $this->assertFormElementNotValid($element, '-1', GreaterThan::NOT_GREATER);
        $this->assertFormElementNotValid($element, '0', GreaterThan::NOT_GREATER);
        $this->assertFormElementValid($element, '1');
        $this->assertFormElementValid($element, '9999999999');
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
