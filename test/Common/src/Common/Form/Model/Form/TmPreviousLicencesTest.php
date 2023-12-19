<?php

namespace CommonTest\Form\Model\Form;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class TmPreviousLicencesTest
 *
 * @group FormTests
 */
class TmPreviousLicencesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\TmPreviousLicences::class;

    public function testId()
    {
        $element = ['tm-previous-licences-details', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testVersion()
    {
        $element = ['tm-previous-licences-details', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testLicNo()
    {
        $element = ['tm-previous-licences-details', 'licNo'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 1, 18);
    }

    public function testHolderName()
    {
        $element = ['tm-previous-licences-details', 'holderName'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 1, 90);
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
