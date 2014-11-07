<?php

/**
 * Test fee waive note input filter
 *
 * @author Alex Peshkov <alex.peshkov@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\FeeWaiveNote;

/**
 * Test fee waive note input filter
 *
 * @author Alex Peshkov <alex.peshkov@clocal.co.uk>
 */
class FeeWaiveNoteTest extends PHPUnit_Framework_TestCase
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
        $this->element = new FeeWaiveNote();
    }

    /**
     * Test validators
     * @group feeWaiveNote
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();
        $this->assertEquals($spec['validators'][0]['name'], '\Zend\Validator\StringLength');
        $this->assertEquals($spec['validators'][1]['name'], '\Zend\Validator\NotEmpty');
    }
}
