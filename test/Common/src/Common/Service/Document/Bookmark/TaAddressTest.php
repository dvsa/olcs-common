<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TaAddress;

/**
 * TA Address test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaAddressTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TaAddress();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new TaAddress();
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
}
