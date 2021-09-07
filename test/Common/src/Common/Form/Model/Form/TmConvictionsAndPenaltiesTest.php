<?php

namespace CommonTest\Form\Model\Form;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class TmConvictionsAndPenaltiesTest
 *
 * @group FormTests
 */
class TmConvictionsAndPenaltiesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\TmConvictionsAndPenalties::class;

    public function testId()
    {
        $element = ['tm-convictions-and-penalties-details', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testVersion()
    {
        $element = ['tm-convictions-and-penalties-details', 'version'];
        $this->assertFormElementHidden($element);
    }

    /**
     * @todo unskip https://jira.dvsacloud.uk/browse/VOL-2309
     */
    public function testConvictionDate()
    {
        $this->markTestSkipped();
        $element = ['tm-convictions-and-penalties-details', 'convictionDate'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDate($element);
    }

    public function testCategoryText()
    {
        $element = ['tm-convictions-and-penalties-details', 'categoryText'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 1, 1024);
    }

    public function testNotes()
    {
        $element = ['tm-convictions-and-penalties-details', 'notes'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 1, 4000);
    }

    public function testCourtFpn()
    {
        $element = ['tm-convictions-and-penalties-details', 'courtFpn'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 1, 70);
    }

    public function testPenalty()
    {
        $element = ['tm-convictions-and-penalties-details', 'penalty'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 1, 255);
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
