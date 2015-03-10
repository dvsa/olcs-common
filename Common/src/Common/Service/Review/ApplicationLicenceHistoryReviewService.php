<?php

/**
 * Application Licence History Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Review;

use Common\Service\Entity\PreviousLicenceEntityService;

/**
 * Application Licence History Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationLicenceHistoryReviewService extends AbstractReviewService
{

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $mainItems = [];

        $previousLicences = $this->splitLicencesUp($data['previousLicences']);

        $mainItems[] = $this->formatCurrentLicences(
            $previousLicences[PreviousLicenceEntityService::TYPE_CURRENT], $data['prevHasLicence']
        );

        $mainItems[] = $this->formatLicences(
            $previousLicences[PreviousLicenceEntityService::TYPE_APPLIED], $data['prevHadLicence'], 'applied'
        );

        $mainItems[] = $this->formatLicences(
            $previousLicences[PreviousLicenceEntityService::TYPE_REFUSED], $data['prevBeenRefused'], 'refused'
        );

        $mainItems[] = $this->formatLicences(
            $previousLicences[PreviousLicenceEntityService::TYPE_REVOKED], $data['prevBeenRevoked'], 'revoked'
        );

        $mainItems[] = $this->formatLicences(
            $previousLicences[PreviousLicenceEntityService::TYPE_PUBLIC_INQUIRY],
            $data['prevBeenAtPi'],
            'public-inquiry'
        );

        $mainItems[] = $this->formatDisqualifiedLicences(
            $previousLicences[PreviousLicenceEntityService::TYPE_DISQUALIFIED], $data['prevBeenDisqualifiedTc']
        );

        $mainItems[] = $this->formatHeldLicences(
            $previousLicences[PreviousLicenceEntityService::TYPE_HELD], $data['prevPurchasedAssets']
        );

        return [
            'subSections' => [
                [
                    'mainItems' => $mainItems
                ]
            ]
        ];
    }

    private function splitLicencesUp($previousLicences)
    {
        $previousLicenceList = [
            PreviousLicenceEntityService::TYPE_CURRENT => [],
            PreviousLicenceEntityService::TYPE_APPLIED => [],
            PreviousLicenceEntityService::TYPE_REFUSED => [],
            PreviousLicenceEntityService::TYPE_REVOKED => [],
            PreviousLicenceEntityService::TYPE_PUBLIC_INQUIRY => [],
            PreviousLicenceEntityService::TYPE_DISQUALIFIED => [],
            PreviousLicenceEntityService::TYPE_HELD => []
        ];

        foreach (array_keys($previousLicenceList) as $type) {

            foreach ($previousLicences as $previousLicence) {
                if ($previousLicence['previousLicenceType']['id'] === $type) {
                    $previousLicenceList[$type][] = $previousLicence;
                }
            }
        }

        return $previousLicenceList;
    }

    private function formatCurrentLicences($licences, $answer)
    {
        $config = [
            'header' => 'application-review-licence-history-current-title',
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-licence-history-current-question',
                        'value' => $this->formatYesNo($answer)
                    ]
                ]
            ]
        ];

        if ($answer === 'Y') {
            foreach ($licences as $licence) {
                $config['multiItems'][] = [
                    [
                        'label' => 'application-review-licence-history-licence-no',
                        'value' => $licence['licNo']
                    ],
                    [
                        'label' => 'application-review-licence-history-licence-holder',
                        'value' => $licence['holderName']
                    ],
                    [
                        'label' => 'application-review-licence-history-will-surrender',
                        'value' => $this->formatYesNo($licence['willSurrender'])
                    ]
                ];
            }
        }

        return $config;
    }

    private function formatLicences($licences, $answer, $index)
    {
        $config = [
            'header' => 'application-review-licence-history-' . $index . '-title',
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-licence-history-' . $index . '-question',
                        'value' => $this->formatYesNo($answer)
                    ]
                ]
            ]
        ];

        if ($answer === 'Y') {
            foreach ($licences as $licence) {
                $config['multiItems'][] = [
                    [
                        'label' => 'application-review-licence-history-licence-no',
                        'value' => $licence['licNo']
                    ],
                    [
                        'label' => 'application-review-licence-history-licence-holder',
                        'value' => $licence['holderName']
                    ]
                ];
            }
        }

        return $config;
    }

    private function formatDisqualifiedLicences($licences, $answer)
    {
        $config = [
            'header' => 'application-review-licence-history-disqualified-title',
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-licence-history-disqualified-question',
                        'value' => $this->formatYesNo($answer)
                    ]
                ]
            ]
        ];

        if ($answer === 'Y') {
            foreach ($licences as $licence) {
                $config['multiItems'][] = [
                    [
                        'label' => 'application-review-licence-history-licence-no',
                        'value' => $licence['licNo']
                    ],
                    [
                        'label' => 'application-review-licence-history-licence-holder',
                        'value' => $licence['holderName']
                    ],
                    [
                        'label' => 'application-review-licence-history-disqualification-date',
                        'value' => $this->formatDate($licence['disqualificationDate'], 'd/m/Y')
                    ],
                    [
                        'label' => 'application-review-licence-history-disqualification-length',
                        'value' => $licence['disqualificationLength']
                    ]
                ];
            }
        }

        return $config;
    }

    private function formatHeldLicences($licences, $answer)
    {
        $config = [
            'header' => 'application-review-licence-history-held-title',
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-licence-history-held-question',
                        'value' => $this->formatYesNo($answer)
                    ]
                ]
            ]
        ];

        if ($answer === 'Y') {
            foreach ($licences as $licence) {
                $config['multiItems'][] = [
                    [
                        'label' => 'application-review-licence-history-licence-no',
                        'value' => $licence['licNo']
                    ],
                    [
                        'label' => 'application-review-licence-history-licence-holder',
                        'value' => $licence['holderName']
                    ],
                    [
                        'label' => 'application-review-licence-history-purchase-date',
                        'value' => $this->formatDate($licence['purchaseDate'], 'd/m/Y')
                    ]
                ];
            }
        }

        return $config;
    }
}
