<?php

/**
 * Variation Psv Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;

/**
 * Variation Psv Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPsvOcTotalAuthReviewService extends AbstractVariationOcTotalAuthReviewService
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
     * Get the keys of the values to compare
     *
     * @param array $data
     * @return string
     */
    protected function getChangedKeys($data)
    {
        $changedKeys = [
            'totAuthSmallVehicles' => 'vehicles-small',
            'totAuthMediumVehicles' => 'vehicles-medium'
        ];

        if (in_array($data['licenceType']['id'], $this->licenceTypesWithLargeVehicles)) {
            $changedKeys['totAuthLargeVehicles'] = 'vehicles-large';
        }

        $changedKeys['totAuthVehicles'] = 'vehicles';

        if (in_array($data['licenceType']['id'], $this->licenceTypesWithCommunityLicences)) {
            $changedKeys['totCommunityLicences'] = 'community-licences';
        }

        return $changedKeys;
    }
}
