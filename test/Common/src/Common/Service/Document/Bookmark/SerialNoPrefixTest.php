<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\SerialNoPrefix;

/**
 * Serial No Prefix test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class SerialNoPrefixTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new SerialNoPrefix();
        $query = $bookmark->getQuery(['communityLic' => 123]);

        $this->assertEquals('CommunityLic', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNonZeroIssueNumber()
    {
        $bookmark = new SerialNoPrefix();
        $bookmark->setData(
            [
                'serialNoPrefix' => 'foo'
            ]
        );

        $this->assertEquals(
            'foo',
            $bookmark->render()
        );
    }
}
