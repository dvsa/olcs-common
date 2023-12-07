<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

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
        $this->assertFormElementTable(['table', 'table']);
        $this->assertFormElementHidden(['table', 'action']);
        $this->assertFormElementHidden(['table', 'id']);

        $element = ['table', 'rows'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementIsRequired($element);
    }

    public function testSaveAndContinue()
    {
        $this->assertFormElementActionButton(['form-actions', 'saveAndContinue']);
    }

    public function testSave()
    {
        $this->assertFormElementActionButton(['form-actions', 'save']);
    }

    public function testCancel()
    {
        $this->assertFormElementActionButton(['form-actions', 'cancel']);
    }
}
