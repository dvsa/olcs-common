<?php

namespace CommonTest\Common\Form\Model\Form;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class GenericDeleteConfirmationTest
 *
 * @group FormTests
 */
class GenericDeleteConfirmationTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\GenericDeleteConfirmation::class;

    public function testSubmit()
    {
        $element = ['form-actions', 'submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
