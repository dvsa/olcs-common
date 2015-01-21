<?php

/**
 * Test hours per day
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\HoursPerDay;
use Zend\Validator as ZendValidator;

/**
 * Test Hours Per Day Input Filter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class HoursPerDayTest extends PHPUnit_Framework_TestCase
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
        $this->element = new HoursPerDay('hoursMon');
    }

    /**
     * Test validators
     * 
     * @group hoursPerDayInputFilter
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][0] instanceof ZendValidator\Between);
        $options = $spec['validators'][0]->getOptions();
        $messageTemplates = $options['messageTemplates'];
        $message = $messageTemplates[ZendValidator\Between::NOT_BETWEEN];
        $this->assertEquals($message, "Mon must be between '%min%' and '%max%', inclusively");
        $this->assertEquals($options['min'], 0);
        $this->assertEquals($options['max'], 24);
    }
}
