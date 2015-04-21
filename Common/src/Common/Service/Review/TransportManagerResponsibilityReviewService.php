<?php

/**
 * Transport Manager Responsibility Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Data\CategoryDataService;

/**
 * Transport Manager Responsibility Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerResponsibilityReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        return [
            'subSections' => [
                [
                    'mainItems' => [
                        [
                            'multiItems' => [
                                [
                                    [
                                        'label' => 'tm-review-responsibility-operating-centres',
                                        'noEscape' => true,
                                        'value' => $this->formatOperatingCentres($data)
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-tmType',
                                        'value' => $this->formatRefdata($data['tmType'])
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-isOwner',
                                        'value' => $this->formatYesNo($data['isOwner'])
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-mon',
                                        'value' => $data['hoursMon']
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-tue',
                                        'value' => $data['hoursTue']
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-wed',
                                        'value' => $data['hoursWed']
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-thu',
                                        'value' => $data['hoursThu']
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-fri',
                                        'value' => $data['hoursFri']
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-sat',
                                        'value' => $data['hoursSat']
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-sun',
                                        'value' => $data['hoursSun']
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-additional-info',
                                        'value' => $data['additionalInformation']
                                    ],
                                    [
                                        'label' => 'tm-review-responsibility-additional-info-files',
                                        'noEscape' => true,
                                        'value' => $this->formatFiles($data)
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'title' => 'tm-review-responsibility-other-licences',
                    'mainItems' => $this->formatOtherLicences($data)
                ]
            ]
        ];
    }

    private function formatOperatingCentres($data)
    {
        $addresses = [];

        foreach ($data['operatingCentres'] as $oc) {
            $addresses[] = $this->formatShortAddress($oc['address']);
        }

        return implode('<br>', $addresses);
    }

    private function formatFiles($data)
    {
        $files = $this->findFiles(
            $data['transportManager']['documents'],
            CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
        );

        if (empty($files)) {
            return $this->translate('tm-review-responsibility-no-files');
        }

        $fileNames = [];

        foreach ($files as $file) {
            $fileNames[] = $file['filename'];
        }

        return implode('<br>', $fileNames);
    }

    private function formatOtherLicences($data)
    {
        if (empty($data['otherLicences'])) {
            return [
                [
                    'freetext' => $this->translate('tm-review-responsibility-other-licences-none-added')
                ]
            ];
        }

        $mainItems = [];

        foreach ($data['otherLicences'] as $otherLicence) {
            $mainItems[] = [
                'header' => $otherLicence['licNo'],
                'multiItems' => [
                    [
                        [
                            'label' => 'tm-review-responsibility-other-licences-role',
                            'value' => $this->formatRefdata($otherLicence['role'])
                        ],
                        [
                            'label' => 'tm-review-responsibility-other-licences-operating-centres',
                            'value' => $otherLicence['operatingCentres']
                        ],
                        [
                            'label' => 'tm-review-responsibility-other-licences-vehicles',
                            'value' => $otherLicence['totalAuthVehicles']
                        ],
                        [
                            'label' => 'tm-review-responsibility-other-licences-hours-per-week',
                            'value' => $otherLicence['hoursPerWeek']
                        ]
                    ]
                ]
            ];
        }

        return $mainItems;
    }
}
