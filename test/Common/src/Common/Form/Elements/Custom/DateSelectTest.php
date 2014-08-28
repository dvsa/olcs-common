<?php

/**
 * DateSelectTest
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Types;

use Common\Form\Elements\Custom\DateSelect;

/**
 * DateSelectTest
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateSelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the input specification
     */
    public function testGetInputSpecificationWithRequiredAndMaxYearDelta()
    {
        $element = new DateSelect();
        $element->setOptions(array(
            'max_year_delta' => '+11',
            'required' => true
        ));
        $spec = $element->getInputSpecification();

        $baseYear = date('Y');
        $targetYear = date('Y', strtotime('+11 years'));
        $this->assertTrue($spec['required']);
        $this->assertEquals($baseYear, $element->getMinYear());
        $this->assertEquals($targetYear, $element->getMaxYear());
    }

    public function testGetInputSpecificationWithRequiredAndMaxYearDeltaAndValue()
    {
        $element = new DateSelect();
        $element->setOptions(array(
            'max_year_delta' => '+11',
            'required' => true
        ));
        $element->setValue('1990-05-05');
        $spec = $element->getInputSpecification();

        $targetYear = date('Y', strtotime('+11 years'));
        $this->assertTrue($spec['required']);
        $this->assertEquals('1990', $element->getMinYear());
        $this->assertEquals($targetYear, $element->getMaxYear());
    }

    public function testGetInputSpecificationWithRequiredAndMaxYearDeltaAndValueGreaterThanCurrentYear()
    {
        $element = new DateSelect();
        $element->setOptions(array(
            'max_year_delta' => '+11',
            'required' => true
        ));
        $element->setValue('2055-05-05');
        $spec = $element->getInputSpecification();

        $baseYear = date('Y');
        $targetYear = date('Y', strtotime('+11 years'));
        $this->assertTrue($spec['required']);
        $this->assertEquals($baseYear, $element->getMinYear());
        $this->assertEquals($targetYear, $element->getMaxYear());
    }

    public function testGetInputSpecificationNotRequiredStandardMaxYear()
    {
        $element = new DateSelect();
        $spec = $element->getInputSpecification();

        $targetYear = date('Y');
        $this->assertNull($spec['required']);
        $this->assertEquals($targetYear, $element->getMaxYear());
    }
}
