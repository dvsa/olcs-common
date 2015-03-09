<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Trailers;

/**
 * Trailers test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TrailersTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $bookmark = new Trailers();
        $bookmark->setData(
            [
                'totAuthTrailers' => 1234
            ]
        );

        $this->assertEquals(1234, $bookmark->render());
    }
}
