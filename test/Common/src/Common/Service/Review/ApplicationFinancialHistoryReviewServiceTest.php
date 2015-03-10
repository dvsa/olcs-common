<?php

/**
 * Application Financial History Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationFinancialHistoryReviewService;
use Common\Service\Data\CategoryDataService;

/**
 * Application Financial History Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationFinancialHistoryReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationFinancialHistoryReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

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
            'Yeses without documents' => [
                [
                    'bankrupt' => 'Y',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyDetails' => 'Some text in here',
                    'insolvencyConfirmation' => 'Y',
                    'documents' => []
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
                                'label' => 'application-review-financial-history-evidence',
                                'noEscape' => true,
                                'value' => 'application-review-financial-history-evidence-send-translated'
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
            'Yeses with documents' => [
                [
                    'bankrupt' => 'Y',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyDetails' => 'Some text in here',
                    'insolvencyConfirmation' => 'Y',
                    'documents' => [
                        [
                            'filename' => 'evidence1',
                            'category' => [
                                'id' => CategoryDataService::CATEGORY_LICENSING
                            ],
                            'subCategory' => [
                                'id' => CategoryDataService::DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL
                            ]
                        ],
                        [
                            'filename' => 'evidence2',
                            'category' => [
                                'id' => CategoryDataService::CATEGORY_LICENSING
                            ],
                            'subCategory' => [
                                'id' => CategoryDataService::DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL
                            ]
                        ],
                        [
                            'filename' => 'ignore1',
                            'category' => [
                                'id' => 'foo'
                            ],
                            'subCategory' => [
                                'id' => CategoryDataService::DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL
                            ]
                        ],
                        [
                            'filename' => 'ignore2',
                            'category' => [
                                'id' => CategoryDataService::CATEGORY_LICENSING
                            ],
                            'subCategory' => [
                                'id' => 'bar'
                            ]
                        ]
                    ]
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
                                'label' => 'application-review-financial-history-evidence',
                                'noEscape' => true,
                                'value' => 'evidence1<br>evidence2'
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
