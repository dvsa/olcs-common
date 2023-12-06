<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class AddTransportManagerTest
 *
 * @group FormTests
 */
class AddTransportManagerTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\AddTransportManager::class;

    public function testRegisteredUser()
    {
        $element = ['data', 'registeredUser'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementNotValid($element, 'X', [\Laminas\Validator\InArray::NOT_IN_ARRAY]);
    }

    public function testAddUser()
    {
        $element = ['data', 'addUser'];
        $this->assertFormElementActionButton($element);
    }

    public function testContinue()
    {
        $element = ['form-actions', 'continue'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
