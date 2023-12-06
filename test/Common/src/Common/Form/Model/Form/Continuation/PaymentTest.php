<?php

namespace CommonTest\Common\Form\Model\Form\Continuation;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Model\Form\Continuation\Payment;

/**
 * Class PaymentTest
 *
 * @group FormTests
 */
class PaymentTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = Payment::class;

    public function testPay()
    {
        $element = ['form-actions', 'pay'];
        $this->assertFormElementActionButton($element);
    }

    public function testStoredCards()
    {
        $element = ['storedCards', 'card'];
        $this->assertFormElementDynamicSelect($element);
    }

    public function testAmount()
    {
        $element = ['amount'];
        $this->assertFormElementHtml($element);
    }
}
