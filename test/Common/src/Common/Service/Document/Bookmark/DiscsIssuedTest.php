<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\DiscsIssued;

/**
 * Discs Issued test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DiscsIssuedTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new DiscsIssued();
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
        $bookmark = new DiscsIssued();
        $bookmark->setData(
            [
                'psvDiscs' => [
                    [
                        'ceasedDate' => null,
                    ], [
                        'ceasedDate' => null,
                    ], [
                        'ceasedDate' => '2015-01-01',
                    ]
                ]
            ]
        );

        $this->assertEquals(2, $bookmark->render());
    }
}
