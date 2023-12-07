<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class BusinessDetailsSubsidiaryCompanyTest
 *
 * @group FormTests
 */
class BusinessDetailsSubsidiaryCompanyTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\BusinessDetailsSubsidiaryCompany::class;

    public function testId()
    {
        $element = ['data', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testName()
    {
        $element = ['data', 'name'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element);
    }

    public function testCompanyNo()
    {
        $element = ['data', 'companyNo'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementCompanyNumber($element);
    }

    public function testVersion()
    {
        $element = ['data', 'version'];
        $this->assertFormElementHidden($element);
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
