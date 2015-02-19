<?php

/**
 * Traffic Area Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use PHPUnit_Framework_TestCase;
use Common\Service\Review\TrafficAreaReviewService;

/**
 * Traffic Area Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TrafficAreaReviewServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new TrafficAreaReviewService();
    }

    public function testGetConfigFromData()
    {
        $data = [
            'licence' => [
                'trafficArea' => [
                    'name' => 'foo'
                ]
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-traffic-area-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-traffic-area',
                        'value' => 'foo'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
