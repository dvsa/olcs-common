<?php

/**
 * Application Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use PHPUnit_Framework_TestCase;
use Common\Service\Review\ApplicationTypeOfLicenceReviewService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTypeOfLicenceReviewServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApplicationTypeOfLicenceReviewService();
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($data, $expected)
    {
        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function providerGetConfigFromData()
    {
        return [
            [
                [
                    'niFlag' => 'N',
                    'goodsOrPsv' => [
                        'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                    ],
                    'licenceType' => [
                        'description' => 'Standard National'
                    ]
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-type-of-licence-operator-location',
                                'value' => 'Great Britain'
                            ],
                            [
                                'label' => 'application-review-type-of-licence-operator-type',
                                'value' => 'Goods'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-type-of-licence-licence-type',
                                'value' => 'Standard National'
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'niFlag' => 'N',
                    'goodsOrPsv' => [
                        'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                    ],
                    'licenceType' => [
                        'description' => 'Standard International'
                    ]
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-type-of-licence-operator-location',
                                'value' => 'Great Britain'
                            ],
                            [
                                'label' => 'application-review-type-of-licence-operator-type',
                                'value' => 'PSV'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-type-of-licence-licence-type',
                                'value' => 'Standard International'
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'niFlag' => 'Y',
                    'licenceType' => [
                        'description' => 'Standard International'
                    ]
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-type-of-licence-operator-location',
                                'value' => 'Northern Ireland'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-type-of-licence-licence-type',
                                'value' => 'Standard International'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
