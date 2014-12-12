<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\HearingDateTime;
use Common\Data\Object\Publication;

/**
 * Class HearingDateTimeTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class HearingDateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the date and time are set correctly
     */
    public function testFilter()
    {
        $hearingData = [
            'hearingDate' => '2014-03-16 14:30:00',
        ];

        $expectedOutput = [
            'hearingDate' => '2014-03-16 14:30:00',
            'date' => '16 March 2014',
            'time' => '14:30'
        ];

        $input = new Publication(['hearingData' => $hearingData]);
        $sut = new HearingDateTime();

        $output = $sut->filter($input);

        $this->assertEquals($output->offsetGet('hearingData'), $expectedOutput);
    }
}
