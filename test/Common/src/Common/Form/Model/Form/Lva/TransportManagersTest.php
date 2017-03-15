<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class TransportManagersTest
 *
 * @group FormTests
 */
class TransportManagersTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\TransportManagers::class;

    public function testTable()
    {
        $element = ['table', 'table'];
        $this->assertFormElementTable($element);
        $this->assertFormElementNotValid($element, null, ['required']);
    }

    public function testTableAction()
    {
        $element = ['table', 'action'];
        $this->assertFormElementNoRender($element);
    }

    public function testTableRows()
    {
        $element = ['table', 'rows'];
        $this->assertFormElementHidden($element);
    }

    public function testTableId()
    {
        $element = ['table', 'id'];
        $this->assertFormElementNoRender($element);
    }

    public function testSaveAndContinue()
    {
        $element = ['form-actions', 'saveAndContinue'];
        $this->assertFormElementActionButton($element);
    }

    public function testSave()
    {
        $element = ['form-actions', 'save'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
