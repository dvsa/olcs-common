<?php

/**
 * Test OperatingCentreAdPlacedInTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\OperatingCentreAdPlacedIn;

/**
 * Test OperatingCentreAdPlacedInTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreAdPlacedInTest extends PHPUnit_Framework_TestCase
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
        $this->element = new OperatingCentreAdPlacedIn();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertInstanceOf(
            'Common\Form\Elements\Validators\OperatingCentreAdPlacedInValidator',
            $spec['validators'][0]
        );
    }
}
