<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class FinancialHistoryTest
 *
 * @package OlcsTest\FormTest
 * @group ComponentTests
 * @group FormTests
 */
class FinancialHistoryTest extends AbstractFormValidationTestCase
{
    protected $formName = \Common\Form\Model\Form\Lva\FinancialHistory::class;

    public function testFileUpload()
    {
        $element = ['data', 'file'];
        $this->assertFormElementMultipleFileUpload($element);
    }

    public function testId()
    {
        $this->assertFormElementHidden(['data', 'id']);
    }

    public function testVersion()
    {
        $this->assertFormElementHidden(['data', 'version']);
    }

    public function testFinanceHint()
    {
        $this->assertFormElementHtml(['data', 'financeHint']);
    }

    public function testHasAnyPerson()
    {
        $this->assertFormElementHtml(['data', 'hasAnyPerson']);
    }

    public function testBankrupt()
    {
        $this->assertFormElementIsRequired(['data', 'bankrupt'], true);
    }

    public function testLiquidation()
    {
        $this->assertFormElementIsRequired(['data', 'liquidation'], true);
    }

    public function testReceivership()
    {
        $this->assertFormElementIsRequired(['data', 'receivership'], true);
    }

    public function testAdministration()
    {
        $this->assertFormElementIsRequired(['data', 'administration'], true);
    }

    public function testDisqualified()
    {
        $this->assertFormElementIsRequired(['data', 'disqualified'], true);
    }

    public function testAdditionalInfoLabel()
    {
        $this->assertFormElementHtml(['data', 'additionalInfoLabel']);
    }

    public function testInsolvencyDetails()
    {
        $this->assertFormElementAllowEmpty(['data', 'insolvencyDetails'], true);
    }

    public function testInsolvencyConfirmation()
    {
        $this->assertFormElementIsRequired(
            ['data', 'financialHistoryConfirmation', 'insolvencyConfirmation'],
            true
        );
    }

    public function testNiFlag()
    {
        $this->assertFormElementHidden(['data', 'niFlag']);
    }

    public function testSaveAndContinue()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'saveAndContinue']
        );
    }

    public function testSave()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'save']
        );
    }

    public function testCancel()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'cancel']
        );
    }
}
