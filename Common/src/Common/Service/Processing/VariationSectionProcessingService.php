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

    protected $sectionCompletion;
    protected $applicationId;
    protected $variationCompletionData;

    protected $requireAttentionMap = [
        'business_details' => [
            'business_type'
        ],
        'addresses' => [
            'type_of_licence',
            'business_type'
        ],
        'people' => [
            'business_type'
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

    protected $bespokeRulesMap = [
        'operating_centres' => 'updateRelatedOperatingCentreSections'
    ];

    protected $sectionUpdatedCheckMap = [
        'type_of_licence' => 'hasUpdatedTypeOfLicence',
        'business_type' => 'hasSavedSection',
        'business_details' => 'hasSavedSection',
        'addresses' => 'hasSavedSection',
        'people' => 'hasSavedSection', // May change
        'operating_centres' => 'hasUpdatedOperatingCentres'
    ];

    public function completeSection($applicationId, $section)
    {
        $this->getSectionCompletion($applicationId);

        if ($this->hasSectionChanged($applicationId, $section)) {
            $this->sectionCompletion[$section] = VariationCompletionEntityService::STATUS_UPDATED;
        } else {
            $this->sectionCompletion[$section] = VariationCompletionEntityService::STATUS_UNCHANGED;
        }

        $this->updateSectionsRequiringAttention($section);

        $this->applyBespokeRules($applicationId, $section);

        $this->getServiceLocator()->get('Entity\VariationCompletion')
            ->updateCompletionStatuses($applicationId, $this->sectionCompletion);
    }

    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * Sorry for the double negative here, for some reason this makes the most sense
     *
     * @param string $section
     * @return boolean
     */
    public function isNotUnchanged($section)
    {
        $this->getSectionCompletion($this->applicationId);

        return isset($this->sectionCompletion[$section])
            && $this->sectionCompletion[$section] != VariationCompletionEntityService::STATUS_UNCHANGED;
    }

    /**
     * Method to call the corresponding business rule
     *
     * @param int $applicationId
     * @param string $section
     * @return boolean
     */
    public function hasSectionChanged($applicationId, $section)
    {
        return $this->{$this->sectionUpdatedCheckMap[$section]}($applicationId);
    }

    /**
     * Business rules to check if the TOL section has been updated
     *
     * @param int $applicationId
     * @return boolean
     */
    public function hasUpdatedTypeOfLicence($applicationId)
    {
        $data = $this->getVariationCompletionStatusData($applicationId);

        return $data['licenceType']['id'] !== $data['licence']['licenceType']['id'];
    }

    /**
     * Business rules to check if the OC section has been updated
     *
     * @param int $applicationId
     * @return boolean
     */
    public function hasUpdatedOperatingCentres($applicationId)
    {
        $data = $this->getVariationCompletionStatusData($applicationId);

        if (!empty($data['operatingCentres'])) {
            return true;
        }

        $comparisons = [
            'totAuthVehicles',
            'totAuthTrailers',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ];

        foreach ($comparisons as $comparison) {
            if ($data[$comparison] != $data['licence'][$comparison]) {
                return true;
            }
        }

        return false;
    }

    /**
     * A generic callback that marks a section as complete
     *
     * @param int $applicationId
     * @return boolean
     */
    public function hasSavedSection($applicationId)
    {
        return true;
    }

    /**
     * Fetch and cache the data required for checking update statuses
     *
     * @param int $applicationId
     * @return array
     */
    protected function getVariationCompletionStatusData($applicationId)
    {
        if ($this->variationCompletionData === null) {
            $this->variationCompletionData = $this->getServiceLocator()->get('Entity\Application')
                ->getVariationCompletionStatusData($applicationId);
        }

        return $this->variationCompletionData;
    }

    /**
     * Fetch and cache the current section completions
     *
     * @param int $applicationId
     * @return array
     */
    protected function getSectionCompletion($applicationId)
    {
        if ($this->sectionCompletion === null) {
            $this->sectionCompletion = $this->getServiceLocator()->get('Entity\VariationCompletion')
                ->getCompletionStatuses($applicationId);
        }

        return $this->sectionCompletion;
    }

    /**
     * Apply the generic rules on sections requiring attension
     *
     * @param string $currentSection
     */
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

    /**
     * Some sections have more complicated rules, we hook into thoses here
     *
     * @param int $applicationId
     * @param string $section
     */
    protected function applyBespokeRules($applicationId, $section)
    {
        if (isset($this->bespokeRulesMap[$section])) {
            $this->{$this->bespokeRulesMap[$section]}($applicationId);
        }
    }

    /**
     * Apply the operating centre rules
     *
     * @param int $applicationId
     */
    protected function updateRelatedOperatingCentreSections($applicationId)
    {
        // @todo add rules
    }
}
