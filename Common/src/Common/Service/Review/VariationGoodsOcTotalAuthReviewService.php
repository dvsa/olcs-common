<?php

/**
 * Variation Goods Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;

/**
 * Variation Goods Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsOcTotalAuthReviewService extends AbstractVariationOcTotalAuthReviewService
{
    /**
     * Get the keys of the values to compare
     *
     * @param array $data
     * @return string
     */
    protected function getChangedKeys($data)
    {
        $changedKeys = [
            'totAuthVehicles' => 'vehicles',
            'totAuthTrailers' => 'trailers'
        ];

        if ($data['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
            $changedKeys['totCommunityLicences'] = 'community-licences';
        }

        return $changedKeys;
    }
}
