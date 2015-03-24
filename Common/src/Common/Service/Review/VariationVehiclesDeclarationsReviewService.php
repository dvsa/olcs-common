<?php

/**
 * Variation Vehicles Declarations Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Variation Vehicles Declarations Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesDeclarationsReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return $this->getServiceLocator()->get('Review\ApplicationVehiclesDeclarations')->getConfigFromData($data);
    }
}
