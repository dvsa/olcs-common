<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\FeeDueDate;

/**
 * Fee Due Date test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeeDueDateTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new FeeDueDate();
        $query = $bookmark->getQuery(['fee' => 123]);

        $this->assertEquals('Fee', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new FeeDueDate();
        $bookmark->setData(
            [
                'invoicedDate' => '2015-01-01'
            ]
        );

        $dateHelper = $this->getMock('\Common\Service\Helper\DateHelperService', ['calculateDate']);
        $dateHelper->expects($this->once())
            ->method('calculateDate')
            ->with('2015-01-01', 15, true, true)
            ->willReturn('2015-01-15');

        $bookmark->setDateHelper($dateHelper);

        $this->assertEquals(
            '15/01/2015',
            $bookmark->render()
        );
    }
}
