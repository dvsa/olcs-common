<?php

/**
 * Application Financial History Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Data\CategoryDataService;

/**
 * Application Financial History Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationFinancialHistoryReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $config = [
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-financial-history-bankrupt',
                        'value' => $this->formatYesNo($data['bankrupt'])
                    ]
                ],
                [
                    [
                        'label' => 'application-review-financial-history-liquidation',
                        'value' => $this->formatYesNo($data['liquidation'])
                    ]
                ],
                [
                    [
                        'label' => 'application-review-financial-history-receivership',
                        'value' => $this->formatYesNo($data['receivership'])
                    ]
                ],
                [
                    [
                        'label' => 'application-review-financial-history-administration',
                        'value' => $this->formatYesNo($data['administration'])
                    ]
                ],
                [
                    [
                        'label' => 'application-review-financial-history-disqualified',
                        'value' => $this->formatYesNo($data['disqualified'])
                    ]
                ]
            ]
        ];

        $showAdditionalInfo = false;

        $questions = ['bankrupt', 'liquidation', 'receivership', 'administration', 'disqualified'];

        foreach ($questions as $question) {
            if ($data[$question] == 'Y') {
                $showAdditionalInfo = true;
                break;
            }
        }

        if ($showAdditionalInfo) {
            $config['multiItems'][] = [
                [
                    'label' => 'application-review-financial-history-insolvencyDetails',
                    'value' => $data['insolvencyDetails']
                ]
            ];

            $config['multiItems'][] = [
                [
                    'label' => 'application-review-financial-history-evidence',
                    'noEscape' => true,
                    'value' => $this->formatEvidence($data)
                ]
            ];
        }

        $config['multiItems'][] = [
            [
                'label' => 'application-review-financial-history-insolvencyConfirmation',
                'value' => $this->formatConfirmed($data['insolvencyConfirmation'])
            ]
        ];

        return $config;
    }

    private function formatEvidence($data)
    {
        $files = $this->findFiles(
            $data['documents'],
            CategoryDataService::CATEGORY_LICENSING,
            CategoryDataService::DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL
        );

        if (empty($files)) {
            return $this->translate('application-review-financial-history-evidence-send');
        }

        $fileNames = [];

        foreach ($files as $file) {
            $fileNames[] = $file['filename'];
        }

        return implode('<br>', $fileNames);
    }
}
