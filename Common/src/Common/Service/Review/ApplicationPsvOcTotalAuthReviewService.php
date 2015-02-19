<?php

/**
 * Application Psv Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;

/**
 * Application Psv Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPsvOcTotalAuthReviewService extends AbstractReviewService
{
    private $licenceTypesWithLargeVehicles = [
        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
    ];

    private $licenceTypesWithCommunityLicences = [
        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
    ];

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
                    // We name the large vehicle node so we can easily remove it later if we need to
                    'large' => [
                        'label' => 'review-operating-centres-authorisation-vehicles-large',
                        'value' => $data['totAuthLargeVehicles']
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => $data['totAuthVehicles']
                    ]
                ]
            ]
        ];

        // @NOTE here we use a slightly different method to modify the config, we REMOVE the large vehicles,
        // everywhere else we generally only add relevant nodes, but due to where the large vehicles node needs to sit
        // it's easier to conditionally remove it, also there is no real processing involved in adding it initially so
        // this isn't really a big deal
        if (!in_array($data['licenceType']['id'], $this->licenceTypesWithLargeVehicles)) {
            unset($config['multiItems'][0]['large']);
        }

        // Conditionally add the community licences, if the licence type is restricted or standard international
        if (in_array($data['licenceType']['id'], $this->licenceTypesWithCommunityLicences)) {
            $config['multiItems'][0][] = [
                'label' => 'review-operating-centres-authorisation-community-licences',
                'value' => $data['totCommunityLicences']
            ];
        }

        return $config;
    }
}
