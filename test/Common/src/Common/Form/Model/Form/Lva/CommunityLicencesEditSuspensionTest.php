<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class CommunityLicencesEditSuspensionTest
 *
 * @group FormTests
 */
class CommunityLicencesEditSuspensionTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\CommunityLicencesEditSuspension::class;

    public function testReasons()
    {
        $element = [ 'data', 'reasons' ];
        $this->assertFormElementDynamicSelect($element);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testStatus()
    {
        $element = [ 'data', 'status' ];
        $this->assertFormElementHidden($element);
    }

    public function testId()
    {
        $element = [ 'data', 'id' ];
        $this->assertFormElementHidden($element);
    }

    public function testVersion()
    {
        $element = [ 'data', 'version' ];
        $this->assertFormElementHidden($element);
    }

    public function testActionButtons()
    {
        $element = [ 'form-actions', 'submit' ];
        $this->assertFormElementActionButton($element);

        $element = [ 'form-actions', 'cancel' ];
        $this->assertFormElementActionButton($element);
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
        $futureYear = date('Y')+2;

        $element = [ 'dates', 'endDate' ];

        $errorMessages = [
            'todayOrInFuture',
        ];

        $this->assertFormElementValid($element, ['day' => 1, 'month' => '2', 'year' => $futureYear]);
        $this->assertFormElementNotValid($element, ['day' => '1', 'month' => '1', 'year' => 1999], $errorMessages);
    }
}
