<?php

/**
 * LicenceTypeShort formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\LicenceTypeShort;
use Common\Service\Entity\LicenceEntityService;

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
        $this->assertEquals($expected, LicenceTypeShort::format($data));
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
                            'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                        ]
                    ]
                ],
                'GV'
            ],
            'psv' => [
                [
                    'licence' => [
                        'goodsOrPsv' => [
                            'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                        ]
                    ]
                ],
                'PSV'
            ],
            'restricted' => [
                [
                    'licence' => [
                        'licenceType' => [
                            'id' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
                        ]
                    ]
                ],
                'R'
            ],
            'special restricted' => [
                [
                    'licence' => [
                        'licenceType' => [
                            'id' => LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
                        ]
                    ]
                ],
                'SR'
            ],
            'standard national' => [
                [
                    'licence' => [
                        'licenceType' => [
                            'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
                        ]
                    ]
                ],
                'SN'
            ],
            'standard international' => [
                [
                    'licence' => [
                        'licenceType' => [
                            'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                        ]
                    ]
                ],
                'SI'
            ],
            'combined: gv sn' => [
                [
                    'licence' => [
                        'goodsOrPsv' => [
                            'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                        ],
                        'licenceType' => [
                            'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                        ]
                    ]
                ],
                'GV-SI'
            ],
            'combined: psv sr' => [
                [
                    'licence' => [
                        'goodsOrPsv' => [
                            'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                        ],
                        'licenceType' => [
                            'id' => LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
                        ]
                    ]
                ],
                'PSV-SR'
            ],
            'combined: psv sr ON licence' => [
                [
                    'goodsOrPsv' => [
                        'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                    ],
                    'licenceType' => [
                        'id' => LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
                    ]
                ],
                'PSV-SR'
            ]
        ];
    }
}
