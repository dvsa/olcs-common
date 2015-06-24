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
                            'discNo' => '123456'
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
                            'ceasedDate' => '2015-01-01',
                            'discNo' => '123456'
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
                            'ceasedDate' => null,
                            'discNo' => null
                        ]
                    ]
                ],
                'Pending'
            ]
        ];
    }
}
