<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class AddTransportManagerDetailsTest
 *
 * @group FormTests
 */
class AddTransportManagerDetailsTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\AddTransportManagerDetails::class;

    public function testForename()
    {
        $element = ['data', 'forename'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element);
    }

    public function testFamilyName()
    {
        $element = ['data', 'familyName'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element);
    }

    /**
     * @todo unskip https://jira.dvsacloud.uk/browse/VOL-2309
     */
    public function testBirthDate()
    {
        $this->markTestSkipped();
        $element = ['data', 'birthDate'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementDate($element);
    }

    public function testEmail()
    {
        $element = ['data', 'email'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementEmailAddress($element);
    }

    public function testGuidance()
    {
        $element = ['data', 'guidance'];
        $this->assertFormElementHtml($element);
    }

    public function testSend()
    {
        $element = ['form-actions', 'send'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
