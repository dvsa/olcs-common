<?php

namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\CommunityLicence;

/**
 * Community Licence mapper
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicenceMapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testMapFromResult($data)
    {
        $result = CommunityLicence::mapFromResult($data);

        $expected = [
            'dates' => [
                'startDate' => '2016-01-01',
                'endDate' => '2017-01-01'
            ],
            'data' => [
                'id' => 1,
                'version' => 2,
                'reasons' => ['foo', 'bar'],
                'status' => 'cake'
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function dataProvider()
    {
        return [
            [
                [
                    'currentSuspension' => [
                        'startDate' => '2016-01-01',
                        'endDate' => '2017-01-01',
                        'id' => 1,
                        'version' => 2,
                        'reasons' => ['foo', 'bar']
                    ],
                    'status' => [
                        'id' => 'cake'
                    ]
                ]
            ],
            [
                [
                    'futureSuspension' => [
                        'startDate' => '2016-01-01',
                        'endDate' => '2017-01-01',
                        'id' => 1,
                        'version' => 2,
                        'reasons' => ['foo', 'bar']
                    ],
                    'status' => [
                        'id' => 'cake'
                    ]
                ]
            ],
        ];
    }

    public function testMapFromForm()
    {
        $data = [
            'data' => [
                'id' => 1,
                'version' => 2,
                'reasons' => ['foo', 'bar'],
                'status' => 'cake'
            ],
            'dates' => [
                'startDate' => [
                    'day' => '01',
                    'month' => '01',
                    'year' => '2016'
                ],
                'endDate' => [
                    'day' => '01',
                    'month' => '01',
                    'year' => '2017'
                ],
            ]
        ];

        $expected = [
            'id' => 1,
            'version' => 2,
            'communityLicenceId' => 3,
            'reasons' => [
                'foo', 'bar'
            ],
            'status' => 'cake',
            'startDate' => '2016-01-01',
            'endDate' => '2017-01-01'
        ];

        $result = CommunityLicence::mapFromForm($data, 3);
        $this->assertEquals($expected, $result);
    }
}
