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
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
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
    public function testValidatorOrder()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][0] instanceof ZendValidator\Digits);
        $this->assertTrue($spec['validators'][1] instanceof ZendValidator\Between);

        $digitMessages = $spec['validators'][0]->getOptions()['messageTemplates'];

        $betweenOptions = $spec['validators'][1]->getOptions();
        $betweenMessages = $betweenOptions['messageTemplates'];

        $this->assertEquals($betweenOptions['min'], 0);
        $this->assertEquals($betweenOptions['max'], 24);
        $this->assertEquals(
            $betweenMessages[ZendValidator\Between::NOT_BETWEEN],
            "Mon must be between '%min%' and '%max%', inclusively"
        );

        $this->assertEquals(
            $digitMessages[ZendValidator\Digits::NOT_DIGITS],
            "Mon must be a whole number"
        );
    }
}
