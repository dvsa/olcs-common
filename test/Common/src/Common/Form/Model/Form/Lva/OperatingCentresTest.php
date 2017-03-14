<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Elements\Types\TrafficAreaSet;

/**
 * Class OperatingCentresTest
 *
 * @group FormTests
 */
class OperatingCentresTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\OperatingCentres::class;

    public function testTable()
    {
        $element = [ 'table', 'table' ];
        $this->assertFormElementTable($element);
        $this->assertFormElementNotValid($element, '', [ 'required' ]);

        $element = [ 'table', 'action' ];
        $this->assertFormElementHidden($element);

        $element = [ 'table', 'id' ];
        $this->assertFormElementHidden($element);

        $element = [ 'table', 'rows' ];
        $this->assertFormElementHidden($element);
    }

    public function testTrafficAreaSelect()
    {
        $element = [ 'dataTrafficArea', 'trafficArea' ];
        $this->assertFormElementType($element, \Zend\Form\Element\Select::class);
        $this->assertFormElementRequired($element, true);
    }

    public function testTrafficAreaSet()
    {
        $element = [ 'dataTrafficArea', 'trafficAreaSet' ];
        $this->assertFormElementType($element, TrafficAreaSet::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testTrafficAreaEnforcementAreaSelect()
    {
        $element = [ 'dataTrafficArea', 'enforcementArea' ];
        $this->assertFormElementType($element, \Zend\Form\Element\Select::class);
        $this->assertFormElementRequired($element, false);
    }

    public function testVersion()
    {
        $element = [ 'data', 'version' ];
        $this->assertFormElementHidden($element);
    }

    public function testTotalAuthVehicles()
    {
        $element = [ 'data', 'totAuthVehicles' ];
        $this->assertFormElementNumber($element, 1, 1000000, [ \Zend\Validator\Between::NOT_BETWEEN ]);
    }

    public function testTotalAuthTrailers()
    {
        $element = [ 'data', 'totAuthTrailers' ];
        $this->assertFormElementNumber($element, 0, 1000000, [ \Zend\Validator\Between::NOT_BETWEEN ]);
    }

    public function testTotalCommunityLicences()
    {
        $element = [ 'data', 'totCommunityLicences' ];
        $this->assertFormElementNumber($element, 0, 1000000, [ \Zend\Validator\Between::NOT_BETWEEN ]);
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
