<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\DiscList;
use Common\Service\Document\Parser\RtfParser;

/**
 * Disc list test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DiscListTest extends \PHPUnit_Framework_TestCase
{
    /*
    public function testGetQueryContainsExpectedKeys()
    {
    }
     */

    public function testRender()
    {
        $parser = new RtfParser();
        $bookmark = new DiscList();
        $bookmark->setData([]);
        $bookmark->setParser($parser);

        $result = $bookmark->render();
    }
}
