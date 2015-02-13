<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\EuropeanLicenceNumber;

/**
 * European Licence Number test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class EuropeanLicenceNumberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new EuropeanLicenceNumber();
        $query = $bookmark->getQuery(['communityLic' => 123]);

        $this->assertEquals('CommunityLic', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new EuropeanLicenceNumber();
        $bookmark->setData(
            [
                'issueNo' => 2,
                'licence' => [
                    'licNo' => 'PD4345'
                ]

            ]
        );

        $this->assertEquals(
            'PD4345/00002',
            $bookmark->render()
        );
    }
}
