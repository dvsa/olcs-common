<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\CaseworkerDetails;

/**
 * Case worker details test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CaseworkerDetailsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new CaseworkerDetails();
        $query = $bookmark->getQuery(['user' => 123]);

        $this->assertEquals('User', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithContactDetailsAddress()
    {
        $bookmark = new CaseworkerDetails();
        $bookmark->setData(
            [
                'contactDetails' => [
                    'forename' => 'A',
                    'familyName' => 'User',
                    'address' => [
                        'addressLine1' => 'Line 1'
                    ]
                ]
            ]
        );
        $this->assertEquals(
            "A User\nLine 1",
            $bookmark->render()
        );
    }

    public function testRenderWithTrafficAreaContactDetailsAddress()
    {
        $bookmark = new CaseworkerDetails();
        $bookmark->setData(
            [
                'contactDetails' => [
                    'forename' => 'A',
                    'familyName' => 'User',
                    'address' => []
                ],
                'team' => [
                    'trafficArea' => [
                        'contactDetails' => [
                            'address' => [
                                'addressLine1' => 'TA 11'
                            ]
                        ]
                    ]
                ]
            ]
        );
        $this->assertEquals(
            "A User\nTA 11",
            $bookmark->render()
        );
    }
}
