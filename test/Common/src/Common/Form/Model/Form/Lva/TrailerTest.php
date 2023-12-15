<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Common\Form\Elements\Types\Radio;
use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;
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

    public function testLongerSemiTrailerWarning()
    {
        $this->assertFormElementHtml(['data','longerSemiTrailer','YContent','longerSemiTrailerWarning']);
    }

    public function testIsLongerSemiTrailer()
    {
        $element = ['data','longerSemiTrailer','isLongerSemiTrailer'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
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
