<?php

/**
 * VehicleDiscNo formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase;
use Common\Service\Table\Formatter\VehicleDiscNo;

/**
 * VehicleDiscNo formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleDiscNoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, VehicleDiscNo::format($data));
    }

    public function provider()
    {
        return [
            [
                [
                    'specifiedDate' => null,
                    'removalDate' => null
                ],
                'Pending'
            ],
            [
                [
                    'specifiedDate' => '2015-01-01',
                    'removalDate' => null
                ],
                ''
            ],
            [
                [
                    'specifiedDate' => null,
                    'removalDate' => '2015-01-01'
                ],
                ''
            ],
            [
                [
                    'specifiedDate' => '2015-01-01',
                    'removalDate' => '2015-01-01'
                ],
                ''
            ],
            [
                [
                    'specifiedDate' => '2015-01-01',
                    'removalDate' => null,
                    'goodsDiscs' => [
                        [
                            'id' => 551,
                            'discNo' => '123456',
                            'ceasedDate' => null,
                        ]
                    ]
                ],
                '123456'
            ],
            [
                [
                    'specifiedDate' => '2015-01-01',
                    'removalDate' => null,
                    'goodsDiscs' => [
                        [
                            'id' => 55,
                            'discNo' => '123456',
                            'ceasedDate' => '2016-11-15',
                        ],
                        [
                            'id' => 4,
                            'ceasedDate' => '2015-01-01',
                            'discNo' => 'X111'
                        ]
                    ]
                ],
                ''
            ],
            [
                [
                    'specifiedDate' => '2015-01-01',
                    'removalDate' => null,
                    'goodsDiscs' => [
                        [
                            'id' => 55,
                            'ceasedDate' => null,
                            'discNo' => null
                        ],
                        [
                            'id' => 4,
                            'ceasedDate' => '2015-01-01',
                            'discNo' => 'X111'
                        ]
                    ]
                ],
                'Pending'
            ],
            [
                [
                    'specifiedDate' => '2015-01-01',
                    'removalDate' => null,
                    'goodsDiscs' => [
                        [
                            'id' => 55,
                            'ceasedDate' => null,
                            'discNo' => 'XX9999'
                        ],
                        [
                            'id' => 4,
                            'ceasedDate' => '2016-11-15',
                            'discNo' => 'XX1123'
                        ],
                    ]
                ],
                'XX9999'
            ],
        ];
    }
}
