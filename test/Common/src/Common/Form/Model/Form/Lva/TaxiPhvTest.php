<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\Form\Element\Select;
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
        $this->assertFormElementTable(['table', 'table']);
        $this->assertFormElementHidden(['table', 'action']);
        $this->assertFormElementHidden(['table', 'id']);

        $element = ['table', 'rows'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementIsRequired($element);
    }

    public function testTrafficArea()
    {
        $element = ['dataTrafficArea', 'trafficArea'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementIsRequired($element, true);
    }

    public function testTrafficAreaSet()
    {
        $element = ['dataTrafficArea', 'trafficAreaSet'];
        $this->assertFormElementType($element, TrafficAreaSet::class);
        $this->assertFormElementIsRequired($element, false);
    }

    public function testEnforcementArea()
    {
        $element = ['dataTrafficArea', 'enforcementArea'];
        $this->assertFormElementType($element, Select::class);
        $this->assertFormElementIsRequired($element, false);
    }

    public function testSaveAndContinue()
    {
        $this->assertFormElementActionButton(['form-actions', 'saveAndContinue']);
    }

    public function testSave()
    {
        $this->assertFormElementActionButton(['form-actions', 'save']);
    }

    public function testCancel()
    {
        $this->assertFormElementActionButton(['form-actions', 'cancel']);
    }
}
