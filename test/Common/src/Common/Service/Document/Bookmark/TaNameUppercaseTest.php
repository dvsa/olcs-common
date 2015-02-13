<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TaNameUppercase;

/**
 * TA Name (uppercase) test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaNameUppercaseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TaNameUppercase();
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
        $bookmark = new TaNameUppercase();
        $bookmark->setData(
            [
                'trafficArea' => [
                    'name' => 'TA Name 1'
                ]
            ]
        );

        $this->assertEquals(
            'TA NAME 1',
            $bookmark->render()
        );
    }
}
