<?php

/**
 * Goods Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;

/**
 * Goods Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsOcTotalAuthReviewService extends AbstractReviewService
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
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => $data['totAuthVehicles']
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-trailers',
                        'value' => $data['totAuthTrailers']
                    ]
                ]
            ]
        ];

        if ($data['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
            $config['multiItems'][0][] = [
                'label' => 'review-operating-centres-authorisation-community-licences',
                'value' => $data['totCommunityLicences']
            ];
        }

        return $config;
    }
}
