<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\SerialNum;

/**
 * Class SerialNumTest
 *
 * Test the serial number bookmark.
 *
 * @package CommonTest\Service\Document\Bookmark
 */
class SerialNumTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new SerialNum();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);
        $this->assertEquals(['id' => 123], $query['data']);
    }

    public function testRender()
    {
        $mock = $this->getMock('Common\Service\Helper\DateHelperService');
        $mock->expects($this->once())
            ->method('getDate')
            ->with('d/m/Y H:i:s')
            ->willReturn('01/02/15 12:34:56');

        $bookmark = new SerialNum();
        $bookmark->setData(
            [
                'licNo' => 123
            ]
        );

        $bookmark->setDateHelper($mock);

        // The date function is used here because there is no easy way to get
        // a reference to the service container.
        $this->assertEquals(
            '123 01/02/15 12:34:56',
            $bookmark->render()
        );
    }
}
