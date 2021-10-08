<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Laminas\Form\Element\Radio;

/**
 * Class TypeOfLicenceTest
 *
 * @group FormTests
 */
class TypeOfLicenceTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\TypeOfLicence::class;

    public function testVersion()
    {
        $element = ['version'];
        $this->assertFormElementHidden($element);
    }

    public function testOperatorLocation()
    {
        $element = ['type-of-licence', 'operator-location'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testOperatorType()
    {
        $element = ['type-of-licence', 'operator-type'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testLicenceType()
    {
        $element = ['type-of-licence', 'licence-type', 'licence-type'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testVehicleType()
    {
        $element = ['type-of-licence', 'licence-type', 'ltyp_siContent', 'vehicle-type'];
        $this->assertFormElementType($element, Radio::class);
        $this->assertFormElementRequired($element, true);
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
