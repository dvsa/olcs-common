<?php

/**
 * Venue Address formatter test
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\VenueAddress;
use PHPUnit_Framework_TestCase;

/**
 * Venue Address formatter test
 */
class VenueAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group VenueAddressFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, VenueAddress::format($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'venue' => [
                [
                    'venue' => [
                        'name' => 'venue',
                        'address' => [
                            'addressLine1' => 'a1',
                            'addressLine2' => 'a2',
                            'addressLine3' => 'a3',
                            'addressLine4' => 'a4',
                            'town' => 'town',
                            'postcode' => 'PO12 6ST',
                        ],
                    ],
                    'venueOther' => null,
                ],
                'venue - a1, a2, a3, a4, town, PO12 6ST',
            ],
            'otherVenue' => [
                [
                    'venue' => null,
                    'venueOther' => 'other venue',
                ],
                'other venue',
            ],
            'other' => [
                [
                    'venue' => null,
                    'venueOther' => null,
                ],
                '',
            ],
        ];
    }
}
