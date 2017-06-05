<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Elements\Types\AttachFilesButton;
use Common\Form\Elements\InputFilters\ActionButton;

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
        $element = ['evidence', 'files', 'file'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementType($element, AttachFilesButton::class);

        $element = ['evidence', 'files', '__messages__'];
        $this->assertFormElementHidden($element);

        $element = ['evidence', 'files', 'upload'];
        $this->assertFormElementType($element, ActionButton::class);
        $this->assertFormElementRequired($element, false);
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
