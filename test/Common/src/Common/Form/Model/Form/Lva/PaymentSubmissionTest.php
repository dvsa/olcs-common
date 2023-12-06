<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class PaymentSubmissionTest
 *
 * @group FormTests
 */
class PaymentSubmissionTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\PaymentSubmission::class;

    public function testVersion()
    {
        $element = ['version'];
        $this->assertFormElementHidden($element);
    }

    public function testDescription()
    {
        $element = ['description'];
        $this->assertFormElementHtml($element);
    }

    public function testAmount()
    {
        $element = ['amount'];
        $this->assertFormElementHtml($element);
    }

    public function testSubmitAndPay()
    {
        $element = ['submitPay'];
        $this->assertFormElementActionButton($element);
    }
}
