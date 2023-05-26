<?php

/**
 * LicenceTypeShort formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\LicenceTypeShort;

/**
 * LicenceTypeShort formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTypeShortTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, (new LicenceTypeShort())->format($data));
    }

    public function provider()
    {
        return [
            'nothing set' => [
                [
                    'licence'
                ],
                ''
            ],
            'gv' => [
                [
                    'licence' => [
                        'goodsOrPsv' => [
                            'id' => RefData::LICENCE_CATEGORY_GOODS_VEHICLE
                        ]
                    ]
                ],
                'GV'
            ],
            'psv' => [
                [
                    'licence' => [
                        'goodsOrPsv' => [
                            'id' => RefData::LICENCE_CATEGORY_PSV
                        ]
                    ]
                ],
                'PSV'
            ],
            'restricted' => [
                [
                    'licence' => [
                        'licenceType' => [
                            'id' => RefData::LICENCE_TYPE_RESTRICTED
                        ]
                    ]
                ],
                'R'
            ],
            'special restricted' => [
                [
                    'licence' => [
                        'licenceType' => [
                            'id' => RefData::LICENCE_TYPE_SPECIAL_RESTRICTED
                        ]
                    ]
                ],
                'SR'
            ],
            'standard national' => [
                [
                    'licence' => [
                        'licenceType' => [
                            'id' => RefData::LICENCE_TYPE_STANDARD_NATIONAL
                        ]
                    ]
                ],
                'SN'
            ],
            'standard international' => [
                [
                    'licence' => [
                        'licenceType' => [
                            'id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                        ]
                    ]
                ],
                'SI'
            ],
            'combined: gv sn' => [
                [
                    'licence' => [
                        'goodsOrPsv' => [
                            'id' => RefData::LICENCE_CATEGORY_GOODS_VEHICLE
                        ],
                        'licenceType' => [
                            'id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                        ]
                    ]
                ],
                'GV-SI'
            ],
            'combined: psv sr' => [
                [
                    'licence' => [
                        'goodsOrPsv' => [
                            'id' => RefData::LICENCE_CATEGORY_PSV
                        ],
                        'licenceType' => [
                            'id' => RefData::LICENCE_TYPE_SPECIAL_RESTRICTED
                        ]
                    ]
                ],
                'PSV-SR'
            ],
            'combined: psv sr ON licence' => [
                [
                    'goodsOrPsv' => [
                        'id' => RefData::LICENCE_CATEGORY_PSV
                    ],
                    'licenceType' => [
                        'id' => RefData::LICENCE_TYPE_SPECIAL_RESTRICTED
                    ]
                ],
                'PSV-SR'
            ]
        ];
    }
}
