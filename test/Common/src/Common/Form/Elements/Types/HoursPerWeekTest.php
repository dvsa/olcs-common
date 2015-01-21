<?php

/**
 * Hours per week
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Types;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Types\HoursPerWeek;

/**
 * Hours per week
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class HoursPerWeekTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the element configuration
     * 
     * @group hoursPerWeekType
     */
    public function testElement()
    {
        $element = new HoursPerWeek();
        $element->setOptions(
            [
                'label' => 'Label',
                'subtitle' => 'Subtitle'
            ]
        );

        $this->assertEquals('Label', $element->getLabel());

        $this->assertTrue($element->has('hoursPerWeekContent'));

        $hoursPerWeek = $element->get('hoursPerWeekContent');
        $this->assertEquals('Subtitle', $hoursPerWeek->getLabel());

        $this->assertTrue($element->get('hoursPerWeekContent')->has('hoursMon'));
        $this->assertTrue($element->get('hoursPerWeekContent')->has('hoursTue'));
        $this->assertTrue($element->get('hoursPerWeekContent')->has('hoursWed'));
        $this->assertTrue($element->get('hoursPerWeekContent')->has('hoursThu'));
        $this->assertTrue($element->get('hoursPerWeekContent')->has('hoursFri'));
        $this->assertTrue($element->get('hoursPerWeekContent')->has('hoursSat'));
        $this->assertTrue($element->get('hoursPerWeekContent')->has('hoursSun'));

        $this->assertEquals(
            'days-of-week-short-mon', $element->get('hoursPerWeekContent')->get('hoursMon')->getLabel()
        );
        $this->assertEquals(
            'days-of-week-short-tue', $element->get('hoursPerWeekContent')->get('hoursTue')->getLabel()
        );
        $this->assertEquals(
            'days-of-week-short-wed', $element->get('hoursPerWeekContent')->get('hoursWed')->getLabel()
        );
        $this->assertEquals(
            'days-of-week-short-thu', $element->get('hoursPerWeekContent')->get('hoursThu')->getLabel()
        );
        $this->assertEquals(
            'days-of-week-short-fri', $element->get('hoursPerWeekContent')->get('hoursFri')->getLabel()
        );
        $this->assertEquals(
            'days-of-week-short-sat', $element->get('hoursPerWeekContent')->get('hoursSat')->getLabel()
        );
        $this->assertEquals(
            'days-of-week-short-sun', $element->get('hoursPerWeekContent')->get('hoursSun')->getLabel()
        );
    }

    /**
     * Test get messages
     * 
     * @group hoursPerWeekType
     */
    public function testGetMessages()
    {
        $element = new HoursPerWeek();
        $element->setMessages('messages');
        $this->assertEquals('messages', $element->getMessages());
    }
}
