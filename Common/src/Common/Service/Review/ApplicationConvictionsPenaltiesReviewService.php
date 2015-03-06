<?php

/**
 * Application Convictions Penalties Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Application Convictions Penalties Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationConvictionsPenaltiesReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        if ($data['prevConviction'] == 'N') {
            return [
                'multiItems' => [
                    [
                        [
                            'label' => 'application-review-convictions-penalties-question',
                            'value' => $this->formatYesNo($data['prevConviction'])
                        ]
                    ],
                    [
                        [
                            'label' => 'application-review-convictions-penalties-confirmation',
                            'value' => $this->formatConfirmed($data['convictionsConfirmation'])
                        ]
                    ]
                ]
            ];
        }

        $mainItems = [];

        foreach ($data['previousConvictions'] as $conviction) {

            $mainItems[] = [
                'header' => $conviction['forename'] . ' ' . $conviction['familyName'],
                'multiItems' => [
                    [
                        [
                            'label' => 'application-review-convictions-penalties-conviction-title',
                            'value' => $conviction['title']
                        ],
                        [
                            'label' => 'application-review-convictions-penalties-conviction-forename',
                            'value' => $conviction['forename']
                        ],
                        [
                            'label' => 'application-review-convictions-penalties-conviction-familyName',
                            'value' => $conviction['familyName']
                        ],
                        [
                            'label' => 'application-review-convictions-penalties-conviction-convictionDate',
                            'value' => $this->formatDate($conviction['convictionDate'], 'd/m/Y')
                        ],
                        [
                            'label' => 'application-review-convictions-penalties-conviction-offence',
                            'value' => $conviction['categoryText']
                        ],
                        [
                            'label' => 'application-review-convictions-penalties-conviction-offence-details',
                            'value' => $conviction['notes']
                        ],
                        [
                            'label' => 'application-review-convictions-penalties-conviction-offence-court',
                            'value' => $conviction['courtFpn']
                        ],
                        [
                            'label' => 'application-review-convictions-penalties-conviction-offence-penalty',
                            'value' => $conviction['penalty']
                        ]
                    ]
                ]
            ];
        }

        $mainItems[] = [
            'multiItems' => [
                [], // Adds a separator
                [
                    [
                        'label' => 'application-review-convictions-penalties-confirmation',
                        'value' => $this->formatConfirmed($data['convictionsConfirmation'])
                    ]
                ]
            ]
        ];

        return [
            'subSections' => [
                [
                    'mainItems' => $mainItems
                ]
            ]
        ];
    }
}
