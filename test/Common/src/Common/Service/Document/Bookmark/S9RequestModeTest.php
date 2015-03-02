<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark as BookmarkNs;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class S9RequestModeTest extends TestCase
{
    public function testAlias()
    {
        $sut = new BookmarkNs\S9RequestMode();

        $this->assertInstanceOf('Common\Service\Document\Bookmark\StatementContactType', $sut);
    }
}
