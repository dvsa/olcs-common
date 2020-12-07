<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\Validator\Digits;
use Laminas\Validator\GreaterThan;

/**
 * Class PsvDiscsRequestTest
 *
 * @group FormTests
 */
class PsvDiscsRequestTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\PsvDiscsRequest::class;

    public function testAdditionalDiscs()
    {
        $element = ['data', 'additionalDiscs'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementNumber(
            $element,
            1,
            null,
            [GreaterThan::NOT_GREATER]
        );
        $this->assertFormElementNotValid(
            $element,
            'test',
            [Digits::NOT_DIGITS, GreaterThan::NOT_GREATER]
        );
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }

    public function testSubmit()
    {
        $element = ['form-actions', 'submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testAddAnother()
    {
        $element = ['form-actions', 'addAnother'];
        $this->assertFormElementActionButton($element);
    }
}
