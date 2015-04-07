<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\FeeReqGrantNumber;

/**
 * Fee Request Grant Number test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeeReqGrantNumberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new FeeReqGrantNumber();
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
        $bookmark = new FeeReqGrantNumber();
        $bookmark->setData(
            [
                'id' => 1234,
                'licence' => [
                    'licNo' => 'OH1'
                ]
            ]
        );

        $this->assertEquals(
            'OH1 / 1234',
            $bookmark->render()
        );
    }
}
