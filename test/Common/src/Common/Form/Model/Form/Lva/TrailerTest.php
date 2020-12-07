<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\I18n\Validator\Alnum;

/**
 * Class TrailerTest
 *
 * @group FormTests
 */
class TrailerTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\Trailer::class;

    public function testId()
    {
        $element = ['data', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testVersion()
    {
        $element = ['data', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testTrailerNumber()
    {
        $element = ['data', 'trailerNo'];
        $this->assertFormElementText($element);
        $this->assertFormElementValid($element, 'abc123');
        $this->assertFormElementNotValid(
            $element,
            '!!@@ABC123!!',
            [Alnum::NOT_ALNUM]
        );
    }

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

    public function testAddAnother()
    {
        $element = ['form-actions', 'addAnother'];
        $this->assertFormElementActionButton($element);
    }
}
