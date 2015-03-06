<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TaAddressPhone;

/**
 * TA Address (with phone number) test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaAddressPhoneTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TaAddressPhone();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoPhone()
    {
        $bookmark = new TaAddressPhone();
        $bookmark->setData(
            [
                'trafficArea' => [
                    'name' => 'TA Address 1',
                    'contactDetails' => [
                        'address' => [
                            'addressLine1' => 'Line 1',
                            'addressLine2' => 'Line 2',
                            'addressLine3' => 'Line 3',
                            'addressLine4' => 'Line 4',
                            'postcode' => 'LS2 4DD'
                        ],
                        'phoneContacts' => []
                    ]
                ]
            ]
        );

        $this->assertEquals(
            "TA Address 1\nLine 1\nLine 2\nLine 3\nLine 4\nLS2 4DD",
            $bookmark->render()
        );
    }

    public function testRenderWithNoMatchingPhone()
    {
        $bookmark = new TaAddressPhone();
        $bookmark->setData(
            [
                'trafficArea' => [
                    'name' => 'TA Address 1',
                    'contactDetails' => [
                        'address' => [
                            'addressLine1' => 'Line 1',
                            'addressLine2' => 'Line 2',
                            'addressLine3' => 'Line 3',
                            'addressLine4' => 'Line 4',
                            'postcode' => 'LS2 4DD'
                        ],
                        'phoneContacts' => [
                            [
                                'phoneNumber' => '1234',
                                'phoneContactType' => [
                                    'id' => 'phone_t_home'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals(
            "TA Address 1\nLine 1\nLine 2\nLine 3\nLine 4\nLS2 4DD",
            $bookmark->render()
        );
    }

    public function testRenderWithMatchingPhone()
    {
        $bookmark = new TaAddressPhone();
        $bookmark->setData(
            [
                'trafficArea' => [
                    'name' => 'TA Address 1',
                    'contactDetails' => [
                        'address' => [
                            'addressLine1' => 'Line 1',
                            'addressLine2' => 'Line 2',
                            'addressLine3' => 'Line 3',
                            'addressLine4' => 'Line 4',
                            'postcode' => 'LS2 4DD'
                        ],
                        'phoneContacts' => [
                            [
                                'phoneNumber' => '1234',
                                'phoneContactType' => [
                                    'id' => 'phone_t_tel'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals(
            "TA Address 1\nLine 1\nLine 2\nLine 3\nLine 4\nLS2 4DD\n1234",
            $bookmark->render()
        );
    }
}
