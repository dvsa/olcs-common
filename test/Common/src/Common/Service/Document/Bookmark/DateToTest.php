<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\DateTo;

/**
 * Date To test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateToTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new DateTo();
        $query = $bookmark->getQuery(['communityLic' => 123]);

        $this->assertEquals('CommunityLic', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new DateTo();
        $bookmark->setData(
            [
                'licence' => [
                    'expiryDate' => '2014-02-03 11:12:34'
                ]
            ]
        );

        $this->assertEquals(
            '03/02/2014',
            $bookmark->render()
        );
    }
}
