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
use Common\Service\Entity\LicenceEntityService;

/**
 * Variation Section Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationSectionProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const STATUS_UNCHANGED = VariationCompletionEntityService::STATUS_UNCHANGED;
    const STATUS_UPDATED = VariationCompletionEntityService::STATUS_UPDATED;
    const STATUS_REQUIRES_ATTENTION = VariationCompletionEntityService::STATUS_REQUIRES_ATTENTION;

    protected $sectionCompletion;
    protected $applicationId;
    protected $variationCompletionData = [];
    protected $isPsv;

    protected $requireAttentionMap = [
        'type_of_licence' => [],
        'business_type' => [],
        'business_details' => [
            'business_type'
        ],
        'addresses' => [
            'business_type'
        ],
        'people' => [
            'business_type'
        ],
        'operating_centres' => [],
        'financial_evidence' => [],
        'transport_managers' => [],
        'vehicles' => [],
        'vehicles_psv' => [],
        'vehicles_declarations' => [],
        'discs' => [],
        'community_licences' => [],
        'safety' => [],
        'conditions_undertakings' => [],
        'financial_history' => [],
        'convictions_penalties' => [],
        //'undertakings' => [] We don't want this as there is bespoke rules around setting this status
    ];

    protected $bespokeRulesMap = [
        'type_of_licence' => 'updateRelatedTypeOfLicenceSections',
        'operating_centres' => 'updateRelatedOperatingCentreSections'
    ];

    protected $sectionUpdatedCheckMap = [
        'type_of_licence' => 'hasUpdatedTypeOfLicence',
        'business_type' => 'hasSavedSection',
        'business_details' => 'hasSavedSection',
        'addresses' => 'hasSavedSection',
        'people' => 'hasSavedSection',
        'operating_centres' => 'hasUpdatedOperatingCentres',
        'financial_evidence' => 'hasSavedSection',
        'transport_managers' => 'hasUpdatedTransportManagers',
        'vehicles' => 'hasUpdatedVehicles',
        'vehicles_psv' => 'hasUpdatedVehicles',
        'vehicles_declarations' => 'hasUpdatedVehicleDeclarations',
        'discs' => 'hasSavedSection',
        'community_licences' => 'hasSavedSection',
        'safety' => 'hasSavedSection',
        'conditions_undertakings' => 'hasUpdatedConditionsUndertakings',
        'financial_history' => 'hasUpdatedFinancialHistory',
        // @NOTE Not sure if we need this just yet
        //'licence_history' => 'hasUpdatedLicenceHistory',
        'convictions_penalties' => 'hasUpdatedConvictionsPenalties',
        'undertakings' => 'hasUpdatedUndertakings'
    ];

    /**
     * Setter for application id
     *
     * @param int $applicationId
     * @return VariationSectionProcessingService
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * Getter for application id
     *
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * This method is called when we have updated a section, this method applies the business rules regarding marking
     * relevant sections as requiring attention
     *
     * @param string $section
     */
    public function completeSection($section)
    {
        $this->getSectionCompletion();

        if (!$this->hasSectionChanged($section)) {
            $this->markSectionUnchanged($section);
        } elseif (!$this->isUpdated($section)) {
            $this->markSectionUpdated($section);
            if ($section !== 'undertakings') {
                $this->resetUndertakings();
            }
        }

        $this->updateSectionsRequiringAttention($section);

        $this->applyBespokeRules();

        $this->getServiceLocator()->get('Entity\VariationCompletion')
            ->updateCompletionStatuses($this->getApplicationId(), $this->sectionCompletion);
    }

    /**
     * Check if a section has been updated
     *
     * @param string $section
     * @return boolean
     */
    public function isUpdated($section)
    {
        return $this->isStatus($section, self::STATUS_UPDATED);
    }

    /**
     * Check if the section is unchanged
     *
     * @param string $section
     * @return string
     */
    public function isUnchanged($section)
    {
        return $this->isStatus($section, self::STATUS_UNCHANGED);
    }

    /**
     * Sorry for the double negative here, for some reason this makes the most sense
     *
     * @param string $section
     * @return boolean
     */
    public function isNotUnchanged($section)
    {
        return !$this->isUnchanged($section);
    }

    /**
     * Shared logic to check a sections status
     *
     * @param string $section
     * @param int $status
     * @return boolean
     */
    public function isStatus($section, $status)
    {
        $this->getSectionCompletion();

        return $this->sectionCompletion[$section] === $status;
    }

    /**
     * Method to call the corresponding business rule
     *
     * @param string $section
     * @return boolean
     */
    public function hasSectionChanged($section)
    {
        return $this->{$this->sectionUpdatedCheckMap[$section]}();
    }

    /**
     * Business rules to check if the TOL section has been updated
     *
     * @return boolean
     */
    public function hasUpdatedTypeOfLicence()
    {
        $data = $this->getVariationCompletionStatusData();

        return $data['licenceType']['id'] !== $data['licence']['licenceType']['id'];
    }

    /**
     * Business rules to check if the OC section has been updated
     *
     * @return boolean
     */
    public function hasUpdatedOperatingCentres()
    {
        $data = $this->getVariationCompletionStatusData();

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
     * If we have updated the transport manager section
     *
     * @return boolean
     */
    public function hasUpdatedTransportManagers()
    {
        $data = $this->getVariationCompletionStatusData();

        return !empty($data['transportManagers']);
    }

    /**
     * If we have updated the vehicles section
     *
     * @return boolean
     */
    public function hasUpdatedVehicles()
    {
        $data = $this->getVariationCompletionStatusData();

        return !empty($data['licenceVehicles']);
    }

    /**
     * If we have updated convictions penalties section
     *
     * @return boolean
     */
    public function hasUpdatedConvictionsPenalties()
    {
        $data = $this->getVariationCompletionStatusData();

        if ($data['convictionsConfirmation'] !== 0) {
            return true;
        }

        if ($data['prevConviction'] !== null) {
            return true;
        }

        return false;
    }

    /**
     * If we have updated undertakings
     *
     * @return boolean
     */
    public function hasUpdatedUndertakings()
    {
        $data = $this->getVariationCompletionStatusData();

        return $data['declarationConfirmation'] == "Y";
    }

    /** Not sure if this is needed yet
    public function hasUpdatedLicenceHistory()
    {
        $data = $this->getVariationCompletionStatusData();

        $fields = [
            'prevHasLicence',
            'prevHadLicence',
            'prevBeenRefused',
            'prevBeenRevoked',
            'prevBeenAtPi',
            'prevBeenDisqualifiedTc',
            'prevPurchasedAssets'
        ];

        return $this->hasCompletedFields($data, $fields);
    }*/

    /**
     * If we have updated financial history
     *
     * @return boolean
     */
    public function hasUpdatedFinancialHistory()
    {
        $data = $this->getVariationCompletionStatusData();

        $fields = [
            'bankrupt',
            'administration',
            'disqualified',
            'liquidation',
            'receivership',
            'insolvencyConfirmation',
            'insolvencyDetails'
        ];

        return $this->hasCompletedFields($data, $fields);
    }

    /**
     * If we have updated vehicle declarations
     *
     * @return boolean
     */
    public function hasUpdatedVehicleDeclarations()
    {
        $data = $this->getVariationCompletionStatusData();

        $fields = [
            'psvOperateSmallVhl',
            'psvSmallVhlNotes',
            'psvSmallVhlConfirmation',
            'psvNoSmallVhlConfirmation',
            'psvLimousines',
            'psvNoLimousineConfirmation',
            'psvOnlyLimousinesConfirmation'
        ];

        return $this->hasCompletedFields($data, $fields);
    }

    /**
     * If we have updated conditions undertakings
     *
     * @return boolean
     */
    public function hasUpdatedConditionsUndertakings()
    {
        $data = $this->getVariationCompletionStatusData();

        return !empty($data['conditionUndertakings']);
    }

    /**
     * A generic callback that marks a section as complete
     *
     * @return boolean
     */
    public function hasSavedSection()
    {
        return true;
    }

    /**
     * Fetch and cache the data required for checking update statuses
     *
     * @return array
     */
    protected function getVariationCompletionStatusData($applicationId = null)
    {
        if ($applicationId === null) {
            $applicationId = $this->getApplicationId();
        }

        if (!isset($this->variationCompletionData[$applicationId])) {
            $this->variationCompletionData[$applicationId] = $this->getServiceLocator()->get('Entity\Application')
                ->getVariationCompletionStatusData($applicationId);
        }

        return $this->variationCompletionData[$applicationId];
    }

    /**
     * Fetch and cache the current section completions
     *
     * @return array
     */
    public function getSectionCompletion()
    {
        if ($this->sectionCompletion === null) {
            $this->sectionCompletion = $this->getServiceLocator()->get('Entity\VariationCompletion')
                ->getCompletionStatuses($this->getApplicationId());
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

            if ($section === $currentSection || $this->isUpdated($section)) {
                continue;
            }

            $this->markSectionUnchanged($section);

            foreach ($triggers as $trigger) {
                if ($this->isUpdated($trigger)) {
                    $this->markSectionRequired($section);
                }
            }
        }
    }

    /**
     * Some sections have more complicated rules, we hook into these here
     */
    protected function applyBespokeRules()
    {
        foreach ($this->bespokeRulesMap as $section => $callback) {
            if ($this->isUpdated($section)) {
                $this->$callback();
            }
        }
    }

    /**
     * Apply the operating centre rules
     */
    protected function updateRelatedOperatingCentreSections()
    {
        $data = $this->getVariationCompletionStatusData();

        // If the financial evidence section is unchanged (Not requires attention or updated)
        // ...and we have increased the total auth vehicles
        if ($this->isUnchanged('financial_evidence') && $this->hasTotAuthVehiclesIncreased($data)) {
            $this->markSectionRequired('financial_evidence');
        }

        $vehSection = $this->getRelevantVehicleSection();

        // If the vehicle section is unchanged AND the totAuthVehicles has dropped below the number of vehicles added
        if ($this->isUnchanged($vehSection) && $this->hasTotAuthVehiclesDroppedBelowVehicleCount($data)) {
            $this->markSectionRequired($vehSection);
        }

        // PSV rules only
        if ($this->isPsv()) {
            // If the discs section is unchanged AND the totAuthVehicles has dropped below the number of discs added
            if ($this->isUnchanged('discs') && $this->hasTotAuthVehiclesDroppedBelowDiscsCount($data)) {
                $this->markSectionRequired('discs');
            }

            // If the vehicles declaration section is unchanged and any of the tot auth vehicle columns has increased
            if ($this->isUnchanged('vehicles_declarations') && $this->hasAnyTotAuthIncreased($data)) {
                $this->markSectionRequired('vehicles_declarations');
            }
        }
    }

    /**
     * Check if any of the auth fields have increased
     *
     * @param array $data
     * @return boolean
     */
    protected function hasAnyTotAuthIncreased($data)
    {
        $allAuths = [
            'totAuthVehicles',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ];

        foreach ($allAuths as $authKey) {
            if ($data[$authKey] > $data['licence'][$authKey]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether the total auth vehicles has been increased
     *
     * @param array $data
     * @return boolean
     */
    protected function hasTotAuthVehiclesIncreased($data)
    {
        $totAuthVehicles = $this->getTotAuthVehicles($data);
        $totAuthLicenceVehicles = $this->getTotAuthVehicles($data['licence']);

        return $totAuthVehicles > $totAuthLicenceVehicles;
    }

    /**
     * Check whether the total auth vehicles has dropped below the number of vehicles added
     *
     * @param array $data
     * @return boolean
     */
    protected function hasTotAuthVehiclesDroppedBelowVehicleCount($data)
    {
        $totAuthVehicles = $this->getTotAuthVehicles($data);

        $totVehicles = $this->countVehicles($data['licence']['licenceVehicles']);

        return $totAuthVehicles < $totVehicles;
    }

    protected function countVehicles($vehicles)
    {
        $count = 0;

        foreach ($vehicles as $vehicle) {
            if (empty($vehicle['removalDate'])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Check whether the total auth vehicles has dropped below the number of discs added
     *
     * @param array $data
     * @return boolean
     */
    protected function hasTotAuthVehiclesDroppedBelowDiscsCount($data)
    {
        $totAuthVehicles = $this->getTotAuthVehicles($data);
        $totDiscs = count($data['licence']['psvDiscs']);

        return $totAuthVehicles < $totDiscs;
    }

    /**
     * Grab the tot vehicle auths for both application and licence
     *
     * @param array $data
     * @return array
     */
    protected function getTotAuthVehicles($data)
    {
        if ($this->isPsv()) {
            return $data['totAuthSmallVehicles'] + $data['totAuthMediumVehicles'] + $data['totAuthLargeVehicles'];
        }

        return $data['totAuthVehicles'];
    }

    /**
     * Return the section name of the vehicle section based on whether the licence is goods or psv
     *
     * @return string
     */
    protected function getRelevantVehicleSection()
    {
        if ($this->isPsv()) {
            return 'vehicles_psv';
        }

        return 'vehicles';
    }

    /**
     * Check whether the licence is psv
     *
     * @return boolean
     */
    protected function isPsv()
    {
        if ($this->isPsv === null) {
            $data = $this->getVariationCompletionStatusData();

            $this->isPsv = $data['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_PSV;
        }

        return $this->isPsv;
    }

    /**
     * Mark a section as required
     *
     * @param string $section
     */
    protected function markSectionRequired($section)
    {
        $this->markSectionStatus($section, self::STATUS_REQUIRES_ATTENTION);
    }

    /**
     * Mark a section as unchanged
     *
     * @param string $section
     */
    protected function markSectionUnchanged($section)
    {
        $this->markSectionStatus($section, self::STATUS_UNCHANGED);
    }

    /**
     * Mark a section as updated
     *
     * @param string $section
     */
    protected function markSectionUpdated($section)
    {
        $this->markSectionStatus($section, self::STATUS_UPDATED);
    }

    /**
     * Mark a section with the given status
     *
     * @param string $section
     * @param int $status
     */
    protected function markSectionStatus($section, $status)
    {
        $this->sectionCompletion[$section] = $status;
    }

    /**
     * If we have completed at least 1 of the fields in the list
     *
     * @param array $data
     * @param array $fields
     * @return boolean
     */
    protected function hasCompletedFields($data, $fields)
    {
        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reset the undertakings section
     */
    protected function resetUndertakings()
    {
        $data = [
            'declarationConfirmation' => 'N'
        ];

        $this->getServiceLocator()->get('Entity\Application')->forceUpdate($this->getApplicationId(), $data);
        $this->markSectionRequired('undertakings');
    }

    /**
     * Apply bespoke type of licence rules
     */
    protected function updateRelatedTypeOfLicenceSections()
    {
        // If the old licence type was restricted and it is being upgraded
        if ($this->isLicenceUpgrade()) {

            $relatedSections = [
                'addresses',
                'transport_managers',
                'financial_history',
                'convictions_penalties'
            ];

            foreach ($relatedSections as $section) {
                if ($this->isUnchanged($section)) {
                    $this->markSectionRequired($section);
                }
            }
        }
    }

    /**
     * If the application involves a licence upgrade
     *
     * @param int $applicationId
     * @return boolean
     */
    public function isLicenceUpgrade($applicationId = null)
    {
        $data = $this->getVariationCompletionStatusData($applicationId);

        $restrictedUpgrades = [
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        return (
            $data['licence']['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED
            && in_array($data['licenceType']['id'], $restrictedUpgrades)
        );
    }

    public function isRealUpgrade($applicationId)
    {
        // If we have upgraded from restricted
        if ($this->isLicenceUpgrade($applicationId)) {
            return true;
        }

        $data = $this->getVariationCompletionStatusData($applicationId);

        // If we have upgraded from stand nat, to stand inter
        if ($data['licence']['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
            && $data['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
            return true;
        }

        return false;
    }
}
