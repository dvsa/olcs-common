<?php

/**
 * Variation Type Of Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Variation Type Of Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTypeOfLicenceReviewService extends AbstractVariationReviewService
{
    protected $sectionName = 'type_of_licence';

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        // Re-use application here
    }
}
