<?php

/**
 * Application Convictions Penalties Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationConvictionsPenaltiesReviewService;

/**
 * Application Convictions Penalties Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationConvictionsPenaltiesReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApplicationConvictionsPenaltiesReviewService();
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
                    'prevConviction' => 'N',
                    'convictionsConfirmation' => 'Y'
                ],
                [
                    'multiItems' => [
                    [
                        [
                            'label' => 'application-review-convictions-penalties-question',
                            'value' => 'No'
                        ]
                    ],
                    [
                        [
                            'label' => 'application-review-convictions-penalties-confirmation',
                            'value' => 'Confirmed'
                        ]
                    ]
                ]
                ]
            ],
            [
                [
                    'prevConviction' => 'Y',
                    'convictionsConfirmation' => 'Y',
                    'previousConvictions' => [
                        [
                            'forename' => 'Bob',
                            'familyName' => 'Smith',
                            'title' => 'Mr',
                            'convictionDate' => '1989-08-23',
                            'categoryText' => 'Some crime',
                            'notes' => 'Details about crime',
                            'courtFpn' => 'Some court',
                            'penalty' => 'Slapped wrist'
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'Bob Smith',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-convictions-penalties-conviction-title',
                                                'value' => 'Mr'
                                            ],
                                            [
                                                'label' => 'application-review-convictions-penalties-conviction-forename',
                                                'value' => 'Bob'
                                            ],
                                            [
                                                'label' => 'application-review-convictions-penalties-conviction-familyName',
                                                'value' => 'Smith'
                                            ],
                                            [
                                                'label' => 'application-review-convictions-penalties-conviction-convictionDate',
                                                'value' => '23/08/1989'
                                            ],
                                            [
                                                'label' => 'application-review-convictions-penalties-conviction-offence',
                                                'value' => 'Some crime'
                                            ],
                                            [
                                                'label' => 'application-review-convictions-penalties-conviction-offence-details',
                                                'value' => 'Details about crime'
                                            ],
                                            [
                                                'label' => 'application-review-convictions-penalties-conviction-offence-court',
                                                'value' => 'Some court'
                                            ],
                                            [
                                                'label' => 'application-review-convictions-penalties-conviction-offence-penalty',
                                                'value' => 'Slapped wrist'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'multiItems' => [
                                        [],
                                        [
                                            [
                                                'label' => 'application-review-convictions-penalties-confirmation',
                                                'value' => 'Confirmed'
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
