<?php

/**
 * Variation Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Variation Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentresReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        $config = ['subSections' => []];

        $isPsv = $this->isPsv($data);

        if ($isPsv) {
            $ocService = $this->getServiceLocator()->get('Review\PsvOperatingCentre');
            $authService = $this->getServiceLocator()->get('Review\VariationPsvOcTotalAuth');
        } else {
            $ocService = $this->getServiceLocator()->get('Review\GoodsOperatingCentre');
            $authService = $this->getServiceLocator()->get('Review\VariationGoodsOcTotalAuth');
        }

        $added = $updated = $deleted = [];

        foreach ($data['operatingCentres'] as $operatingCentre) {
            switch ($operatingCentre['action']) {
                case 'A':
                    $added[] = $ocService->getConfigFromData($operatingCentre);
                    break;
                case 'U':
                    $updated[] = $ocService->getConfigFromData($operatingCentre);
                    break;
                case 'D':
                    $deleted[] = $ocService->getConfigFromData($operatingCentre);
            }
        }

        if (!empty($added)) {
            $config['subSections'][] = [
                'title' => 'variation-review-operating-centres-added-title',
                'mainItems' => $added
            ];
        }

        if (!empty($updated)) {
            $config['subSections'][] = [
                'title' => 'variation-review-operating-centres-updated-title',
                'mainItems' => $updated
            ];
        }

        if (!empty($deleted)) {
            $config['subSections'][] = [
                'title' => 'variation-review-operating-centres-deleted-title',
                'mainItems' => $deleted
            ];
        }

        $config['subSections'][] = [
            'mainItems' => [
                $authService->getConfigFromData($data)
            ]
        ];

        return $config;
    }
}
