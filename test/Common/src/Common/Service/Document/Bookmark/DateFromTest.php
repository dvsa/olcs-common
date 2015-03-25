<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\DateFrom;
use Common\Service\Entity\ApplicationEntityService;

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
        $query = $bookmark->getQuery(['communityLic' => 123, 'application' => 456]);

        $this->assertEquals('CommunityLic', $query[0]['service']);
        $this->assertEquals('Application', $query[1]['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query[0]['data']
        );

        $this->assertEquals(
            [
                'id' => 456
            ],
            $query[1]['data']
        );
    }

    public function testRender()
    {
        $bookmark = new DateFrom();
        $bookmark->setData(
            [
                [
                    'specifiedDate' => '2014-02-03 11:12:34'
                ],
                [
                    'Count' => 0, 'Results' => []
                ]
            ]
        );

        $this->assertEquals(
            '03/02/2014',
            $bookmark->render()
        );
    }

    public function testRenderWithInterim()
    {
        $bookmark = new DateFrom();
        $bookmark->setData(
            [
                [
                    'specifiedDate' => '2014-02-03 11:12:34'
                ],
                [
                    'interimStatus' => [
                        'id' => ApplicationEntityService::INTERIM_STATUS_INFORCE
                    ],
                    'interimStart' => '2011-01-01 10:10:10'
                ]
            ]
        );

        $this->assertEquals(
            '01/01/2011',
            $bookmark->render()
        );
    }
}
