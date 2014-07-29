<?php

/**
 * Test OperatingCentreDateAdPlacedTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\OperatingCentreDateAdPlaced;

/**
 * Test OperatingCentreDateAdPlacedTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreDateAdPlacedTest extends PHPUnit_Framework_TestCase
{
    /**+
     * Holds the element
     */
    private $element;

    /**
     * Setup the element
     */
    public function setUp()
    {
        $this->element = new OperatingCentreDateAdPlaced();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertInstanceOf(
            'Common\Form\Elements\Validators\OperatingCentreDateAdPlacedValidator',
            $spec['validators'][0]
        );
    }
}
