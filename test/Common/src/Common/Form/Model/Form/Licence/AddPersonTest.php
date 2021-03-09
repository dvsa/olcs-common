<?php
namespace CommonTest\Form\Model\Form\Licence;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\Form\Element\Collection;

class AddPersonTest extends AbstractFormValidationTestCase
{
    /** @var string The class name of the form being tested */
    protected $formName = \Common\Form\Model\Form\Licence\AddPerson::class;


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
