<?php

/**
 * Psv Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;

/**
 * Psv Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvOcTotalAuthReviewService extends AbstractReviewService
{
    /**
     * Get total auth config
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $config = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles-small',
                        'value' => $data['totAuthSmallVehicles']
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles-medium',
                        'value' => $data['totAuthMediumVehicles']
                    ],
                    'large' => [
                        'label' => 'review-operating-centres-authorisation-vehicles-large',
                        'value' => $data['totAuthLargeVehicles']
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => $data['totAuthVehicles']
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-community-licences',
                        'value' => $data['totCommunityLicences']
                    ]
                ]
            ]
        ];

        $licenceTypesWithLargeVehicles = [
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        if (!in_array($data['licenceType']['id'], $licenceTypesWithLargeVehicles)) {
            unset($config['multiItems'][0]['large']);
        }

        return $config;
    }
}
