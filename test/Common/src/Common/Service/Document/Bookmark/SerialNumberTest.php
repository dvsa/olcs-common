<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\SerialNumber;

/**
 * Class SerialNumberTest
 *
 * Test the serial number bookmark.
 *
 * @package CommonTest\Service\Document\Bookmark
 */
class SerialNumberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new SerialNumber();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);
        $this->assertEquals(['id' => 123], $query['data']);
    }

    public function testRender()
    {
        $bookmark = new SerialNumber();
        $bookmark->setData(
            [
                'licNo' => 123
            ]
        );

        // The date function is used here because there is no easy way to get
        // a reference to the service container.
        $this->assertEquals(
            '123 ' . date("d/m/Y", strtotime("+0 days")),
            $bookmark->render()
        );
    }
}
