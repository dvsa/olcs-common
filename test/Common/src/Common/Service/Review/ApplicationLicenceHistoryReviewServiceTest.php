<?php

/**
 * Application LicenceHistory Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationLicenceHistoryReviewService;
use Common\Service\Entity\OtherLicenceEntityService;

/**
 * Application LicenceHistory Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationLicenceHistoryReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApplicationLicenceHistoryReviewService();
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
                    'prevHasLicence' => 'N',
                    'prevHadLicence' => 'N',
                    'prevBeenRefused' => 'N',
                    'prevBeenRevoked' => 'N',
                    'prevBeenAtPi' => 'N',
                    'prevBeenDisqualifiedTc' => 'N',
                    'prevPurchasedAssets' => 'N',
                    'otherLicences' => []
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'application-review-licence-history-current-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-current-question',
                                                'value' => 'No'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-applied-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-applied-question',
                                                'value' => 'No'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-refused-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-refused-question',
                                                'value' => 'No'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-revoked-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-revoked-question',
                                                'value' => 'No'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-public-inquiry-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-public-inquiry-question',
                                                'value' => 'No'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-disqualified-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-disqualified-question',
                                                'value' => 'No'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-held-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-held-question',
                                                'value' => 'No'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'prevHasLicence' => 'Y',
                    'prevHadLicence' => 'Y',
                    'prevBeenRefused' => 'Y',
                    'prevBeenRevoked' => 'Y',
                    'prevBeenAtPi' => 'Y',
                    'prevBeenDisqualifiedTc' => 'Y',
                    'prevPurchasedAssets' => 'Y',
                    'otherLicences' => [
                        [
                            'licNo' => '123456789',
                            'holderName' => 'Foo bar',
                            'willSurrender' => 'Y',
                            'previousLicenceType' => ['id' => OtherLicenceEntityService::TYPE_CURRENT],
                        ],
                        [
                            'licNo' => '223456789',
                            'holderName' => 'Foo bar 2',
                            'previousLicenceType' => ['id' => OtherLicenceEntityService::TYPE_APPLIED],
                        ],
                        [
                            'licNo' => '323456789',
                            'holderName' => 'Foo bar 3',
                            'previousLicenceType' => ['id' => OtherLicenceEntityService::TYPE_REFUSED],
                        ],
                        [
                            'licNo' => '423456789',
                            'holderName' => 'Foo bar 4',
                            'previousLicenceType' => ['id' => OtherLicenceEntityService::TYPE_REVOKED],
                        ],
                        [
                            'licNo' => '523456789',
                            'holderName' => 'Foo bar 5',
                            'previousLicenceType' => ['id' => OtherLicenceEntityService::TYPE_PUBLIC_INQUIRY],
                        ],
                        [
                            'licNo' => '623456789',
                            'holderName' => 'Foo bar 6',
                            'disqualificationDate' => '2013-06-20',
                            'disqualificationLength' => '2 Days',
                            'previousLicenceType' => ['id' => OtherLicenceEntityService::TYPE_DISQUALIFIED],
                        ],
                        [
                            'licNo' => '623456789',
                            'holderName' => 'Foo bar 6',
                            'purchaseDate' => '2013-06-20',
                            'previousLicenceType' => ['id' => OtherLicenceEntityService::TYPE_HELD],
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'application-review-licence-history-current-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-current-question',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-licence-history-licence-no',
                                                'value' => '123456789'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-licence-holder',
                                                'value' => 'Foo bar'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-will-surrender',
                                                'value' => 'Yes'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-applied-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-applied-question',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-licence-history-licence-no',
                                                'value' => '223456789'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-licence-holder',
                                                'value' => 'Foo bar 2'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-refused-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-refused-question',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-licence-history-licence-no',
                                                'value' => '323456789'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-licence-holder',
                                                'value' => 'Foo bar 3'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-revoked-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-revoked-question',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-licence-history-licence-no',
                                                'value' => '423456789'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-licence-holder',
                                                'value' => 'Foo bar 4'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-public-inquiry-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-public-inquiry-question',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-licence-history-licence-no',
                                                'value' => '523456789'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-licence-holder',
                                                'value' => 'Foo bar 5'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-disqualified-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-disqualified-question',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-licence-history-licence-no',
                                                'value' => '623456789'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-licence-holder',
                                                'value' => 'Foo bar 6'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-disqualification-date',
                                                'value' => '20/06/2013'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-disqualification-length',
                                                'value' => '2 Days'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-licence-history-held-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-licence-history-held-question',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-licence-history-licence-no',
                                                'value' => '623456789'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-licence-holder',
                                                'value' => 'Foo bar 6'
                                            ],
                                            [
                                                'label' => 'application-review-licence-history-purchase-date',
                                                'value' => '20/06/2013'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
