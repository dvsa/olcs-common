<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class CommunityLicencesTest
 *
 * @group FormTests
 */
class CommunityLicencesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\CommunityLicences::class;

    public function testTotalCommunityLicences()
    {
        $element = [ 'data', 'totalActiveCommunityLicences' ];
        $this->assertFormElementText($element);
        $this->assertFormElementAllowEmpty($element, true);
    }

    public function testTable()
    {
        $element = [ 'table', 'table' ];
        $this->assertFormElementIsRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testRows()
    {
        $element = [ 'table', 'rows' ];
        $this->assertFormElementHidden($element);
    }

    public function testTableId()
    {
        $element = [ 'table', 'id' ];
        $this->assertFormElementHidden($element);
    }

    public function testTableAction()
    {
        $element = [ 'table', 'action' ];
        $this->assertFormElementHidden($element);
    }

    public function testActionButtons()
    {
        $element = [ 'form-actions', 'saveAndContinue' ];
        $this->assertFormElementActionButton($element);

        $element = [ 'form-actions', 'save' ];
        $this->assertFormElementActionButton($element);

        $element = [ 'form-actions', 'cancel' ];
        $this->assertFormElementActionButton($element);
    }
}
