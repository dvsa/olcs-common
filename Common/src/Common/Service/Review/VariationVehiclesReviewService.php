<?php

/**
 * Variation Vehicles Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Variation Vehicles Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $multiItems = [];

        foreach ($data['licenceVehicles'] as $vehicle) {
            $multiItems[] = [
                [
                    'label' => 'application-review-vehicles-vrm',
                    'value' => $vehicle['vehicle']['vrm']
                ],
                [
                    'label' => 'application-review-vehicles-weight',
                    'value' => $this->formatNumber($vehicle['vehicle']['platedWeight']) . ' Kg'
                ]
            ];
        }

        return [
            'subSections' => [
                [
                    'mainItems' => [
                        [
                            'multiItems' => $multiItems
                        ]
                    ]
                ]
            ]
        ];
    }
}
