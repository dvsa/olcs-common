<?php

/**
 * Variation Review Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Entity\VariationCompletionEntityService;

/**
 * Variation Review Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationReviewAdapter extends AbstractReviewAdapter
{
    protected $lva = 'variation';

    /**
     * Sections to remove during filterSections
     *
     * @var array
     */
    protected $sectionsToIgnore = [
        'business_type',
        'business_details',
        'addresses',
        'safety',
        'community_licences'
    ];

    /**
     * Filter unwanted sections
     *
     * @param int $id
     * @param array $sections
     * @return array
     */
    protected function filterSections($id, $sections)
    {
        $completion = $this->getServiceLocator()->get('Entity\VariationCompletion')->getCompletionStatuses($id);

        $filteredSections = parent::filterSections($id, $sections);

        foreach ($filteredSections as $key => $section) {
            if ($completion[$section] !== VariationCompletionEntityService::STATUS_UPDATED) {
                unset($filteredSections[$key]);
            }
        }

        return $filteredSections;
    }
}
