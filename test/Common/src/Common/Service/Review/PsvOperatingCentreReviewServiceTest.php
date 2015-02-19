<?php

/**
 * Psv Operating Centre Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Review;

use PHPUnit_Framework_TestCase;
use Common\Service\Review\PsvOperatingCentreReviewService;

/**
 * Psv Operating Centre Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvOperatingCentreReviewServiceTest extends PHPUnit_Framework_TestCase
{

    protected $sut;

    public function setUp()
    {
        $this->sut = new PsvOperatingCentreReviewService();
    }

    public function testGetConfigFromData()
    {
        $data = [
            'noOfVehiclesRequired' => 10,
            'sufficientParking' => 'Y',
            'permission' => 'N',
            'operatingCentre' => [
                'address' => [
                    'addressLine1' => 'Some building',
                    'addressLine2' => 'Foo street',
                    'town' => 'Bartown',
                    'postcode' => 'FB1 1FB'
                ]
            ]
        ];
        $expected = [
            'header' => 'Some building, Bartown',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centre-address',
                        'value' => 'Some building, Foo street, Bartown, FB1 1FB'
                    ]
                ],
                'vehicles+trailers' => [
                    [
                        'label' => 'review-operating-centre-total-vehicles',
                        'value' => 10
                    ]
                ],
                [
                    [
                        'label' => 'review-operating-centre-sufficient-parking',
                        'value' => 'Confirmed'
                    ]
                ],
                [
                    [
                        'label' => 'review-operating-centre-permission',
                        'value' => 'Unconfirmed'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
