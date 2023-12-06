<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class FinancialEvidenceTest
 *
 * @group FormTests
 */
class FinancialEvidenceTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\FinancialEvidence::class;

    public function testFinancialEvidenceFinance()
    {
        $this->assertFormElementHtml(['finance', 'requiredFinance']);
    }

    public function testEvidenceFile()
    {
        $element = ['evidence', 'files'];
        $this->assertFormElementMultipleFileUpload($element);
    }

    public function testUploadNow()
    {
        $this->assertFormElementIsRequired(['evidence', 'uploadNowRadio'], false);
        $this->assertFormElementIsRequired(['evidence', 'uploadLaterRadio'], false);
        $this->assertFormElementIsRequired(['evidence', 'sendByPostRadio'], false);
    }

    public function testUploadFileCount()
    {
        $this->assertFormElementRequired(
            ['evidence', 'uploadedFileCount'],
            false
        );
    }

    public function testSendByPost()
    {
        $this->assertFormElementHtml(['evidence', 'sendByPost']);
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

    public function testId()
    {
        $element = ['id'];
        $this->assertFormElementHidden($element);
    }

    public function testVersion()
    {
        $element = ['version'];
        $this->assertFormElementHidden($element);
    }

    public function testUploadLater()
    {
        $this->assertFormElementHtml(['evidence', 'uploadLaterMessage']);
    }
}
