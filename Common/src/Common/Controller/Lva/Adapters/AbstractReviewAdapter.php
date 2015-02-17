<?php

/**
 * Abstract Review Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\ReviewAdapterInterface;

/**
 * Abstract Review Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReviewAdapter extends AbstractAdapter implements ReviewAdapterInterface
{
    protected $lva = '';

    /**
     * Get all sections for a given application id
     *
     * @param int $id
     * @param array $relevantSections
     * @return array
     */
    public function getSectionData($id, array $relevantSections = array())
    {
        $entity = ucfirst($this->lva);

        // Grab all of the review data in one go
        $reviewData = $this->getServiceLocator()->get('Entity\\' . $entity)
            ->getReviewData($id, $relevantSections);

        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        $sectionConfig = [];

        foreach ($relevantSections as $section) {
            $serviceName = 'Review\\' . $entity . $stringHelper->underscoreToCamel($section);

            // @NOTE this check is in place while we implement each section
            // eventually we should be able to remove the if
            if ($this->getServiceLocator()->has($serviceName)) {
                $service = $this->getServiceLocator()->get($serviceName);
                $sectionConfig[] = [
                    'header' => $service->getHeader($reviewData),
                    'config' => $service->getConfigFromData($reviewData)
                ];
            }
        }

        return $sectionConfig;
    }
}
