<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\Form\Element\Textarea;

/**
 * Class PreviousConvictionTest
 *
 * @group FormTests
 */
class PreviousConvictionTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\PreviousConviction::class;

    public function testVersion()
    {
        $element = [ 'data', 'version' ];
        $this->assertFormElementHidden($element);
    }

    public function testTitle()
    {
        $element = [ 'data', 'title' ];
        $this->assertFormElementDynamicSelect($element);
    }

    public function testForename()
    {
        $element = [ 'data', 'forename' ];
        $this->assertFormElementText($element);
    }

    public function testFamilyName()
    {
        $element = [ 'data', 'familyName' ];
        $this->assertFormElementText($element);
    }

    public function testConvictionDate()
    {
        $element = ['data', 'convictionDate'];
        $this->assertFormElementNotValid(
            $element,
            [
                'day' => '15',
                'month' => '06',
                'year' => '2060',
            ],
            [ \Common\Form\Elements\Validators\DateNotInFuture::IN_FUTURE ]
        );
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDate($element);
    }

    public function testCategoryText()
    {
        $element = [ 'data', 'categoryText' ];
        $this->assertFormElementText($element);
        $this->assertFormElementRequired($element, true);
    }

    public function testNotes()
    {
        $element = [ 'data', 'notes' ];
        $this->assertFormElementType($element, Textarea::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testCourtFpn()
    {
        $element = [ 'data', 'courtFpn' ];
        $this->assertFormElementText($element);
        $this->assertFormElementRequired($element, true);
    }

    public function testPenalty()
    {
        $element = [ 'data', 'penalty' ];
        $this->assertFormElementText($element);
        $this->assertFormElementRequired($element, true);
    }

    public function testAddAnother()
    {
        $element = ['form-actions', 'addAnother'];
        $this->assertFormElementActionButton($element);
    }

    public function testSave()
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
