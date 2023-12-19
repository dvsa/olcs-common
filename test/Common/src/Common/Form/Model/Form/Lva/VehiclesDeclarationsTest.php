<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Textarea;
use Common\Form\Elements\Types\TermsBox;
use Common\Form\Elements\InputFilters\SingleCheckbox;
use Laminas\Validator\Identical;

/**
 * Class VehiclesDeclarationsTest
 *
 * @group FormTests
 */
class VehiclesDeclarationsTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\VehiclesDeclarations::class;

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

    public function testVersion()
    {
        $element = ['version'];
        $this->assertFormElementHidden($element);
    }

    public function testPsvVehicleSize()
    {
        $element = ['psvVehicleSize', 'size'];
        $this->assertFormElementDynamicRadio($element);
    }

    public function testPsvOperateSmallVhl()
    {
        $element = ['smallVehiclesIntention', 'psvOperateSmallVhl'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
    }

    public function testPsvSmallVhlNotes()
    {
        $element = ['smallVehiclesIntention', 'psvSmallVhlNotes'];
        $this->assertFormElementType($element, Textarea::class);
        $this->assertFormElementRequired($element, false);

        $context = [
            'psvOperateSmallVhl' => 'Y',
            'psvSmallVhlNotes'   => 'ABC',
        ];

        $this->assertFormElementValid($element, 'test', $context);
    }

    public function testPsvSmallVhlScotland()
    {
        $element = ['smallVehiclesIntention', 'psvSmallVhlScotland'];
        $this->assertFormElementType($element, TermsBox::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testPsvSmallVhlUndertakings()
    {
        $element = ['smallVehiclesIntention', 'psvSmallVhlUndertakings'];
        $this->assertFormElementType($element, TermsBox::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testPsvSmallVhlConfirmation()
    {
        $element = ['smallVehiclesIntention', 'psvSmallVhlConfirmation'];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testDeclarationsNineOrMoreLabel()
    {
        $element = ['nineOrMore', 'psvNoSmallVhlConfirmationLabel'];
        $this->assertFormElementHtml($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testDeclarationsNineOrMoreConfirmation()
    {
        $element = ['nineOrMore', 'psvNoSmallVhlConfirmation'];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testPsvMediumVhlConfirmation()
    {
        $element = ['mainOccupation', 'psvMediumVhlConfirmation'];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementNotValid($element, 'N', [Identical::NOT_SAME]);
    }

    public function testPsvMediumVhlNotes()
    {
        $element = ['mainOccupation', 'psvMediumVhlNotes'];
        $this->assertFormElementType($element, Textarea::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testLimousinesNovelyVehiclesRadio()
    {
        $element = ['limousinesNoveltyVehicles', 'psvLimousines'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
    }

    public function testLimousinesNovelyVehiclesConfirmationLabel()
    {
        $element = [
            'limousinesNoveltyVehicles',
            'psvNoLimousineConfirmationLabel',
        ];
        $this->assertFormElementHtml($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testLimousinesNovelyPsvNoLimousineConfirmation()
    {
        $element = ['limousinesNoveltyVehicles', 'psvNoLimousineConfirmation'];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
    }

    public function testNovelyVehiclesPsvOnlyLimousinesConfirmationLabel()
    {
        $element = [
            'limousinesNoveltyVehicles',
            'psvOnlyLimousinesConfirmationLabel',
        ];
        $this->assertFormElementHtml($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testLimousinesNovelyPsvOnlyLimousinesConfirmation()
    {
        $element = [
            'limousinesNoveltyVehicles',
            'psvOnlyLimousinesConfirmation',
        ];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementValid($element, 'Y');
        $this->assertFormElementValid($element, 'N');
    }
}
