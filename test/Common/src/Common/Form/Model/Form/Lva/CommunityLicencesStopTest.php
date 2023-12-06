<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class CommunityLicencesStopTest
 *
 * @group FormTests
 */
class CommunityLicencesStopTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\CommunityLicencesStop::class;

    public function testType()
    {
        $element = [ 'data', 'type' ];
        $this->assertFormElementCheckbox($element);
    }

    public function testReason()
    {
        $element = [ 'data', 'reason' ];
        $this->assertFormElementDynamicSelect($element);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementRequired($element, true);
    }

    public function testStartDate()
    {
        $element = [ 'dates', 'startDate' ];

        $futureYear = date('Y')+1;

        $errorMessages = [
            'todayOrInFuture',
        ];

        $this->assertFormElementValid($element, ['day' => 1, 'month' => '2', 'year' => $futureYear]);
        $this->assertFormElementNotValid($element, ['day' => '1', 'month' => '1', 'year' => 1999], $errorMessages);
    }

    public function testEndDate()
    {
        $element = [ 'dates', 'endDate' ];
        $this->assertFormElementDate($element);
        $this->assertFormElementAllowEmpty($element, true);
    }

    public function testActionButtons()
    {
        $element = [ 'form-actions', 'submit' ];
        $this->assertFormElementActionButton($element);

        $element = [ 'form-actions', 'cancel' ];
        $this->assertFormElementActionButton($element);
    }
}
