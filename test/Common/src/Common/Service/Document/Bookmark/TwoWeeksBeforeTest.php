<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TwoWeeksBefore;

/**
 * Two Weeks Before test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TwoWeeksBeforeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TwoWeeksBefore();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoExpiryDate()
    {
        $bookmark = new TwoWeeksBefore();
        $bookmark->setData(
            [
                'expiryDate' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithTwoWeeksBefore()
    {
        $bookmark = new TwoWeeksBefore();
        $bookmark->setData(
            [
                'expiryDate' => '2014-01-16'
            ]
        );

        $this->assertEquals(
            '02/01/2014',
            $bookmark->render()
        );
    }
}
