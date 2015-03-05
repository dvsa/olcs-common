<?php

/**
 * Variation Financial Evidence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Variation Financial Evidence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationFinancialEvidenceReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $applicationService = $this->getServiceLocator()->get('Review\ApplicationFinancialEvidence');

        return $applicationService->getConfigFromData($data);
    }
}
