<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Checkbox;

/**
 * Class GoodsVehiclesTest
 *
 * @group FormTests
 */
class GoodsVehiclesTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\GoodsVehicles::class;

    public function testVrm()
    {
        $this->assertFormElementHidden(['query', 'vrm']);
    }

    public function testDisc()
    {
        $this->assertFormElementHidden(['query', 'disc']);
    }

    public function testIncludeRemoved()
    {
        $this->assertFormElementHidden(['query', 'includeRemoved']);
    }

    public function testVersion()
    {
        $this->assertFormElementHidden(['data', 'version']);
    }

    public function testHasEnteredReg()
    {
        $element = ['data', 'hasEnteredReg'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testNotice()
    {
        $this->assertFormElementHtml(['data', 'notice']);
    }

    public function testTableTable()
    {
        $element = ['table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
    }

    public function testTableAction()
    {
        $this->assertFormElementNoRender(['table', 'action']);
    }

    public function testTableRows()
    {
        $this->assertFormElementHidden(['table', 'rows']);
    }

    public function testTableId()
    {
        $this->assertFormElementNoRender(['table', 'id']);
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
        $this->assertFormElementActionButton(
            ['form-actions', 'saveAndContinue']
        );
    }

    public function testSave()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'save']
        );
    }

    public function testCancel()
    {
        $this->assertFormElementActionButton(
            ['form-actions', 'cancel']
        );
    }
}
