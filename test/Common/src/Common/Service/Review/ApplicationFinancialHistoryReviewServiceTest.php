<?php

/**
 * Application Financial History Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use PHPUnit_Framework_TestCase;
use Common\Service\Review\ApplicationFinancialHistoryReviewService;

/**
 * Application Financial History Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationFinancialHistoryReviewServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApplicationFinancialHistoryReviewService();
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
            'Nos' => [
                [
                    'bankrupt' => 'N',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyDetails' => '',
                    'insolvencyConfirmation' => 'Y'
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-financial-history-bankrupt',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-liquidation',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-receivership',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-administration',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-disqualified',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-insolvencyConfirmation',
                                'value' => 'Confirmed'
                            ]
                        ]
                    ]
                ]
            ],
            'Yeses' => [
                [
                    'bankrupt' => 'Y',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyDetails' => 'Some text in here',
                    'insolvencyConfirmation' => 'Y'
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-financial-history-bankrupt',
                                'value' => 'Yes'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-liquidation',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-receivership',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-administration',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-disqualified',
                                'value' => 'No'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-insolvencyDetails',
                                'value' => 'Some text in here'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-financial-history-insolvencyConfirmation',
                                'value' => 'Confirmed'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
