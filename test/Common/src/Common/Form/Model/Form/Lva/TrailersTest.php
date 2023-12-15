<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;
use Common\Form\Elements\InputFilters\SingleCheckbox;

/**
 * Class TrailersTest
 *
 * @group FormTests
 */
class TrailersTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\Trailers::class;

    public function testTableTable()
    {
        $element = ['table', 'table'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementTable($element);
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

    public function testTrailersShareInfo()
    {
        $element = ['trailers', 'shareInfo'];
        $this->assertFormElementType($element, SingleCheckbox::class);
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
