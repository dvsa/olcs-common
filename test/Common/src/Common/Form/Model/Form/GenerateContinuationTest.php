<?php

namespace CommonTest\Common\Form\Model\Form;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class GenerateContinuationTest
 *
 * @group FormTests
 */
class GenerateContinuationTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\GenerateContinuation::class;

    public function testType()
    {
        $element = ['details', 'type'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementValid($element, 'operator');
        $this->assertFormElementValid($element, 'irfo');
    }

    public function testDate()
    {
        $element = ['details', 'date'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementMonthSelect($element);
    }

    public function testTrafficArea()
    {
        $element = ['details', 'trafficArea'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testGenerate()
    {
        $element = ['form-actions', 'generate'];
        $this->assertFormElementActionButton($element);
    }
}
