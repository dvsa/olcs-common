<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\OriginalCopy;

/**
 * Original Copy test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class OriginalCopyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new OriginalCopy();
        $query = $bookmark->getQuery(['communityLic' => 123]);

        $this->assertEquals('CommunityLic', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithZeroIssueNumber()
    {
        $bookmark = new OriginalCopy();
        $bookmark->setData(
            [
                'issueNo' => 0
            ]
        );

        $this->assertEquals(
            'LICENCE',
            $bookmark->render()
        );
    }

    public function testRenderWithNonZeroIssueNumber()
    {
        $bookmark = new OriginalCopy();
        $bookmark->setData(
            [
                'issueNo' => 5
            ]
        );

        $this->assertEquals(
            'CERTIFIED TRUE COPY',
            $bookmark->render()
        );
    }
}
