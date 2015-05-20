<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\OpNameOnly;

/**
 * OpNameOnly bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OpNameOnlyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new OpNameOnly();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoOpName()
    {
        $bookmark = new OpNameOnly();
        $bookmark->setData([]);

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRender()
    {
        $bookmark = new OpNameOnly();
        $bookmark->setData(
            [
                'organisation' => [
                    'name' => 'foo'
                ]
            ]
        );

        $this->assertEquals('foo', $bookmark->render());
    }
}
