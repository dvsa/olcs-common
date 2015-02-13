<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\DateFrom;

/**
 * Date From test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateFromTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new DateFrom();
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
        $bookmark = new DateFrom();
        $bookmark->setData(
            [
                'specifiedDate' => '2014-02-03 11:12:34'

            ]
        );

        $this->assertEquals(
            '03/02/2014',
            $bookmark->render()
        );
    }
}
