<?php

namespace CommonTest\Form\Model\Form;

use CommonTest\Common\Controller\Lva\AbstractFormValidationTestCase;

/**
 * Class SearchTest
 *
 * @group FormTests
 */
class SearchTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Search::class;

    public function testLicNo()
    {
        $element = ['search', 'licNo'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testOperatorName()
    {
        $element = ['search', 'operatorName'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testPostcode()
    {
        $element = ['search', 'postcode'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testForename()
    {
        $element = ['search', 'forename'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 2, 35);
    }

    public function testFamilyName()
    {
        $element = ['search', 'familyName'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 2, 35);
    }

    public function testAddress()
    {
        $element = ['advanced', 'address'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementText($element, 10, 100);
    }

    public function testTown()
    {
        $element = ['advanced', 'town'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testCaseNumber()
    {
        $element = ['advanced', 'caseNumber'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testTransportManagerId()
    {
        $element = ['advanced', 'transportManagerId'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testOperatorId()
    {
        $element = ['advanced', 'operatorId'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testVehicleRegMark()
    {
        $element = ['advanced', 'vehicleRegMark'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testDiskSerialNumber()
    {
        $element = ['advanced', 'diskSerialNumber'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testFabsRef()
    {
        $element = ['advanced', 'fabsRef'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testCompanyNo()
    {
        $element = ['advanced', 'companyNo'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element);
    }

    public function testSubmit()
    {
        $element = ['submit'];
        $this->assertFormElementActionButton($element);
    }
}
