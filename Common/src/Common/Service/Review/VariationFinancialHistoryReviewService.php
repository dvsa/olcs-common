<?php

/**
 * Variation Financial History Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Variation Financial History Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationFinancialHistoryReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $applicationService = $this->getServiceLocator()->get('Review\ApplicationFinancialHistory');

        return $applicationService->getConfigFromData($data);
    }
}
