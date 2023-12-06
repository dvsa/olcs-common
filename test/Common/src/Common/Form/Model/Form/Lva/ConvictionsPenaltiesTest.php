<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class ConvictionsPenaltiesTest
 *
 * @group FormTests
 */
class ConvictionsPenaltiesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\ConvictionsPenalties::class;

    public function testTableTable()
    {
        $element = ['data', 'table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testTableAction()
    {
        $this->assertFormElementNoRender(['data', 'table', 'action']);
    }

    public function testTableRows()
    {
        $this->assertFormElementHidden(['data', 'table', 'rows']);
    }

    public function testTableId()
    {
        $this->assertFormElementNoRender(['data', 'table', 'id']);
    }

    public function testVersion()
    {
        $this->assertFormElementHidden(['data', 'version']);
    }

    public function testQuestion()
    {
        $this->assertFormElementRequired(['data', 'question'], true);
    }

    public function testConvictionsConfirmation()
    {
        $this->assertFormElementRequired(
            ['convictionsConfirmation', 'convictionsConfirmation'],
            true
        );
    }

    public function testConvictionsReadMoreLink()
    {
        $this->assertFormElementActionLink(['convictionsReadMoreLink', 'readMoreLink']);
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
