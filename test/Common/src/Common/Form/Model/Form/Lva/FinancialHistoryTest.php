<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Elements\Types\AttachFilesButton;
use Common\Form\Elements\InputFilters\ActionButton;

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
        $element = ['data', 'file', 'file'];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementType($element, AttachFilesButton::class);

        $element = ['data', 'file', '__messages__'];
        $this->assertFormElementHidden($element);

        $element = ['data', 'file', 'upload'];
        $this->assertFormElementType($element, ActionButton::class);
        $this->assertFormElementIsRequired($element, false);
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
