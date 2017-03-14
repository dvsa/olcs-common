<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Zend\Form\Element\Select;
use Common\Form\Elements\Types\TrafficAreaSet;

/**
 * Class TaxiPhvTest
 *
 * @group FormTests
 */
class TaxiPhvTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\TaxiPhv::class;

    public function testTable()
    {
        $element = ['table', 'table'];
        $this->assertFormElementTable($element);
        $this->assertFormElementNotValid($element, null, ['required']);
    }

    public function testTableAction()
    {
        $element = ['table', 'action'];
        $this->assertFormElementNoRender($element);
    }

    public function testTableRows()
    {
        $element = ['table', 'rows'];
        $this->assertFormElementHidden($element);
    }

    public function testTableId()
    {
        $element = ['table', 'id'];
        $this->assertFormElementNoRender($element);
    }

    public function testTrafficArea()
    {
        $element = ['dataTrafficArea', 'trafficArea'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testTrafficAreaSet()
    {
        $element = ['dataTrafficArea', 'trafficAreaSet'];
        $this->assertFormElementType($element, TrafficAreaSet::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testEnforcementArea()
    {
        $element = ['dataTrafficArea', 'enforcementArea'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testSaveAndContinue()
    {
        $element = ['form-actions', 'saveAndContinue'];
        $this->assertFormElementActionButton($element);
    }

    public function testSave()
    {
        $element = ['form-actions', 'save'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
