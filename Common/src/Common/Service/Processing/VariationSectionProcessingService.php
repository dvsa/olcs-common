<?php

/**
 * Variation Section Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\VariationCompletionEntityService;

/**
 * Variation Section Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationSectionProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $requireAttentionMap = [
        'addresses' => [
            'type_of_licence'
        ],
        'transport_managers' => [
            'type_of_licence'
        ],
        'financial_history' => [
            'type_of_licence'
        ],
        'convictions_penalties' => [
            'type_of_licence'
        ]
    ];

    protected $sectionCompletion;
    protected $applicationId;

    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    public function isNotUnchanged($section)
    {
        $this->getSectionCompletion($this->applicationId);

        return isset($this->sectionCompletion[$section])
            && $this->sectionCompletion[$section] != VariationCompletionEntityService::STATUS_UNCHANGED;
    }

    public function completeSection($applicationId, $section)
    {
        $this->getSectionCompletion($applicationId);

        if ($this->hasSectionChanged($applicationId, $section)) {
            $this->sectionCompletion[$section] = VariationCompletionEntityService::STATUS_UPDATED;
        } else {
            $this->sectionCompletion[$section] = VariationCompletionEntityService::STATUS_UNCHANGED;
        }

        $this->updateSectionsRequiringAttention($section);

        $this->getServiceLocator()->get('Entity\VariationCompletion')
            ->updateCompletionStatuses($applicationId, $this->sectionCompletion);
    }

    public function hasSectionChanged($applicationId, $section)
    {
        // @todo for now just return true
        return true;
    }

    protected function getSectionCompletion($applicationId)
    {
        if ($this->sectionCompletion === null) {
            $this->sectionCompletion = $this->getServiceLocator()->get('Entity\VariationCompletion')
                ->getCompletionStatuses($applicationId);
        }

        return $this->sectionCompletion;
    }

    protected function updateSectionsRequiringAttention($currentSection)
    {
        foreach ($this->requireAttentionMap as $section => $triggers) {

            if ($section === $currentSection
                || $this->sectionCompletion[$section] == VariationCompletionEntityService::STATUS_UPDATED
            ) {
                continue;
            }

            $this->sectionCompletion[$section] = VariationCompletionEntityService::STATUS_UNCHANGED;

            foreach ($triggers as $trigger) {
                if ($this->sectionCompletion[$trigger] === VariationCompletionEntityService::STATUS_UPDATED) {
                    $this->sectionCompletion[$section] = VariationCompletionEntityService::STATUS_REQUIRES_ATTENTION;
                }
            }
        }
    }
}
