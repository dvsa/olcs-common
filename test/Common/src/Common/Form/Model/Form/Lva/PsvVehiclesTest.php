<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Checkbox;

/**
 * Class PsvVehiclesTest
 *
 * @group FormTests
 */
class PsvVehiclesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\PsvVehicles::class;

    public function testVersion()
    {
        $element = ['data', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testHasEnteredReg()
    {
        $element = ['data', 'hasEnteredReg'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testVehiclesTable()
    {
        $element = ['vehicles', 'table'];
        $this->assertFormElementTable($element);
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);

        $element = ['vehicles', 'action'];
        $this->assertFormElementHidden($element);

        $element = ['vehicles', 'rows'];
        $this->assertFormElementHidden($element);

        $element = ['vehicles', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testShareInfo()
    {
        $element = ['shareInfo', 'shareInfo'];
        $this->assertFormElementType($element, Checkbox::class);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
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
