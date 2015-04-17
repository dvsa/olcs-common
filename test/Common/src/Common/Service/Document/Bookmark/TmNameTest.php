<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TmName;

/**
 * Transport Manager name bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TmName();
        $query = $bookmark->getQuery(['transportManager' => 123]);

        $this->assertEquals('TransportManager', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new TmName();
        $bookmark->setData(
            [
                'homeCd' => [
                    'person' => [
                        'forename' => 'foo',
                        'familyName' => 'bar'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            'foo bar',
            $bookmark->render()
        );
    }
}
