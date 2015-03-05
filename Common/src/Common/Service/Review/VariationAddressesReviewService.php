<?php

/**
 * Variation Addresses Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Variation Addresses Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationAddressesReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return ['freetext' => $this->translate('variation-review-addresses-change')];
    }
}
