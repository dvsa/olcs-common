<?php

namespace CommonTest\Common\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;
use Common\Form\Elements\Types\TrafficAreaSet;
use Laminas\Validator\Digits;

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
        $this->assertFormElementTable(['table', 'table']);
        $this->assertFormElementHidden(['table', 'action']);
        $this->assertFormElementHidden(['table', 'id']);

        $element = ['table', 'rows'];
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementIsRequired($element);
    }

    public function testTrafficAreaSelect()
    {
        $element = [ 'dataTrafficArea', 'trafficArea' ];
        $this->assertFormElementType($element, \Laminas\Form\Element\Select::class);
        $this->assertFormElementIsRequired($element, true);
    }

    public function testTrafficAreaSet()
    {
        $element = [ 'dataTrafficArea', 'trafficAreaSet' ];
        $this->assertFormElementType($element, TrafficAreaSet::class);
        $this->assertFormElementIsRequired($element, false);
    }

    public function testTrafficAreaEnforcementAreaSelect()
    {
        $element = [ 'dataTrafficArea', 'enforcementArea' ];
        $this->assertFormElementType($element, \Laminas\Form\Element\Select::class);
        $this->assertFormElementIsRequired($element, false);
    }

    public function testVersion()
    {
        $this->assertFormElementHidden([ 'data', 'version' ]);
    }

    public function testTotalAuthHgvVehicles()
    {
        $element = [ 'data', 'totAuthHgvVehiclesFieldset', 'totAuthHgvVehicles' ];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementNumber(
            $element,
            1,
            5000,
            [ \Laminas\Validator\Between::NOT_BETWEEN ]
        );
    }

    public function testTotalAuthLgvVehicles()
    {
        $element = [ 'data', 'totAuthLgvVehiclesFieldset', 'totAuthLgvVehicles' ];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementNumber(
            $element,
            0,
            5000,
            [ \Laminas\Validator\Between::NOT_BETWEEN ]
        );
    }

    public function testTotalAuthTrailers()
    {
        $element = [ 'data', 'totAuthTrailersFieldset', 'totAuthTrailers' ];
        $this->assertFormElementIsRequired($element, true);
        $this->assertFormElementNumber(
            $element,
            0,
            5000,
            [ \Laminas\Validator\Between::NOT_BETWEEN ]
        );
    }

    public function testTotalCommunityLicences()
    {
        $element = [ 'data', 'totCommunityLicencesFieldset', 'totCommunityLicences' ];
        $this->assertFormElementNumber($element, 0, 10000, [ \Laminas\Validator\Between::NOT_BETWEEN ]);
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
