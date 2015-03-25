<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\InterimTrailers;

/**
 * Interim trailers test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimTrailersTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderWithNoValueAppliesDefault()
    {
        $bookmark = new InterimTrailers();
        $bookmark->setData(
            [
                'interimAuthTrailers' => null

            ]
        );

        $this->assertEquals(0, $bookmark->render());
    }
}
