<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Elements\InputFilters\SingleCheckbox;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Textarea;
use Common\Form\Elements\Types\GuidanceTranslated;

/**
 * Class VariationUndertakingsTest
 *
 * @group FormTests
 */
class VariationUndertakingsTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\VariationUndertakings::class;

    public function testDeclarationsAndUndertakingsReview()
    {
        $element = ['declarationsAndUndertakings', 'review'];
        $this->assertFormElementHtml($element);
    }

    public function testDeclarationsAndUndertakingsSummaryDownload()
    {
        $element = ['declarationsAndUndertakings', 'summaryDownload'];
        $this->assertFormElementHtml($element);
    }

    public function testDeclarationsAndUndertakingsInformation()
    {
        $element = ['declarationsAndUndertakings', 'declarationConfirmation'];
        $this->assertFormElementType($element, SingleCheckbox::class);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testDeclarationsAndUndertakingsVersion()
    {
        $element = ['declarationsAndUndertakings', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testDeclarationsAndUndertakingsId()
    {
        $element = ['declarationsAndUndertakings', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testGoodsApplicationApplicationInterim()
    {
        $element = ['interim', 'goodsApplicationInterim'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementAllowEmpty($element, false);
    }

    public function testGoodsApplicationApplicationInterimReason()
    {
        $element = ['interim','YContent','goodsApplicationInterimReason'];
        $this->assertFormElementType($element, Textarea::class);
        $this->assertFormElementAllowEmpty($element, true);
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

    public function testDeclarationsAndUndertakingsInterimInterimFee()
    {
        $element = ['interim', 'YContent', 'interimFee'];
        $this->assertFormElementHtml($element);
    }
}
