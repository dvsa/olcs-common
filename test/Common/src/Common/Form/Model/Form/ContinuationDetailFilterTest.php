<?php

namespace CommonTest\Common\Form\Model\Form;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class ContinuationDetailFilterTest
 *
 * @group FormTests
 */
class ContinuationDetailFilterTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\ContinuationDetailFilter::class;

    public function testLicenceNo()
    {
        $element = ['filters', 'licenceNo'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testLicenceStatus()
    {
        $element = ['filters', 'licenceStatus'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementValid($element, 'lsts_valid');
        $this->assertFormElementValid($element, 'lsts_suspended');
        $this->assertFormElementValid($element, 'lsts_curtailed');
        $this->assertFormElementValid($element, 'lsts_revoked');
        $this->assertFormElementValid($element, 'lsts_surrendered');
        $this->assertFormElementValid($element, 'lsts_terminated');
    }

    public function testMethod()
    {
        $element = ['filters', 'method'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementValid($element, 'all');
        $this->assertFormElementValid($element, 'post');
        $this->assertFormElementValid($element, 'email');
    }

    public function testStatus()
    {
        $element = ['filters', 'status'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementDynamicSelect($element);
    }

    public function testFilter()
    {
        $element = ['filter'];
        $this->assertFormElementActionButton($element);
    }
}
