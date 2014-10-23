<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TaName;

/**
 * TA Name test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TaName();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new TaName();
        $bookmark->setData(
            [
                'trafficArea' => [
                    'name' => 'TA Name 1'
                ]
            ]
        );

        $this->assertEquals(
            'TA Name 1',
            $bookmark->render()
        );
    }
}
