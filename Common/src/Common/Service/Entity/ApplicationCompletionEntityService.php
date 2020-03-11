<?php

namespace Common\Service\Entity;

use Common\Exception\DataServiceException;
use Common\RefData;
use Common\Service\Data\SectionConfig;

/**
 * Application Completion Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationCompletionEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'ApplicationCompletion';

    /**
     * Get completion statuses
     *
     * @param int $applicationId
     * @return array
     */
    public function getCompletionStatuses($applicationId)
    {
        $data = $this->get(array('application' => $applicationId));

        if ($data['Count'] < 1) {
            throw new DataServiceException('Completions status not found');
        }

        if ($data['Count'] > 1) {
            throw new DataServiceException('Too many completion statuses found');
        }

        return $data['Results'][0];
    }

    /**
     * Update completion statuses
     *
     * @NOTE This functionality has been replicated in the API [Application/UpdateApplicationCompletion]
     *
     * @param int $applicationId
     * @param string $currentSection
     */
    public function updateCompletionStatuses($applicationId, $currentSection)
    {
        $completionStatus = $this->getCompletionStatuses($applicationId);

        $completionStatus['application'] = $applicationId;

        $sectionConfig = new SectionConfig();
        $sections = $sectionConfig->getAllReferences();

        $applicationData = $this->getServiceLocator()->get('Entity\Application')
            ->getDataForCompletionStatus($applicationId);

        $filter = $this->getServiceLocator()->get('Helper\String');

        $currentSection = $filter->underscoreToCamel($currentSection);

        foreach ($sections as $section) {
            $section = $filter->underscoreToCamel($section);

            $method = 'get' . $section . 'Status';
            $property = lcfirst($section) . 'Status';

            // Skip sections that are not in the applicationCompletion record
            // These sections could be licence only sections (such as trailers)
            if (!array_key_exists($property, $completionStatus)) {
                continue;
            }

            if ($section === $currentSection || $completionStatus[$property] != RefData::APPLICATION_COMPLETION_STATUS_NOT_STARTED) {
                $completionStatus[$property] = $this->$method($applicationData);
            }
        }

        $this->save($completionStatus);
    }

    private function checkCompletion(array $properties = array())
    {
        $completeCount = 0;

        foreach ($properties as $value) {
            if ($value === true) {
                $completeCount++;
            }
        }

        if ($completeCount === count($properties)) {
            return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
        }

        return RefData::APPLICATION_COMPLETION_STATUS_INCOMPLETE;
    }

    private function isYnValue($value)
    {
        $value = strtoupper($value);

        return ($value === 'Y' || $value === 'N');
    }

    private function isAtLeast1($value)
    {
        return is_numeric($value) && $value > 0;
    }

    /**
     * @todo Need to actually add some logic to this method
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateCommunityLicencesStatus]
     */
    protected function getCommunityLicencesStatus($applicationData)
    {
        return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
    }

    /**
     * Get type of licence status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateTypeOfLicenceStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getTypeOfLicenceStatus($applicationData)
    {
        return $this->checkCompletion(
            array(
                'niFlag' => $this->isYnValue($applicationData['niFlag']),
                'goodsOrPsv' => isset($applicationData['goodsOrPsv']['id']),
                'licenceType' => isset($applicationData['licenceType']['id'])
            )
        );
    }

    /**
     * Get business type status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateBusinessTypeStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getBusinessTypeStatus($applicationData)
    {
        return $this->checkCompletion(
            array(
                !empty($applicationData['licence']['organisation']['type']['id'])
            )
        );
    }

    /**
     * Get business details status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateBusinessDetailsStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getBusinessDetailsStatus($applicationData)
    {
        if (!isset($applicationData['licence']['organisation']['type']['id'])) {
            return RefData::APPLICATION_COMPLETION_STATUS_INCOMPLETE;
        }

        $orgData = $applicationData['licence']['organisation'];

        /**
         * Selectively add required fields based on the org type
         */
        switch ($orgData['type']['id']) {
            case RefData::ORG_TYPE_REGISTERED_COMPANY:
            case RefData::ORG_TYPE_LLP:
                $registeredAddress = !empty($orgData['contactDetails']);

                $requiredVars = array(
                    'name' => isset($orgData['name']),
                    'company' => isset($orgData['companyOrLlpNo']),
                    'registeredAddress' => $registeredAddress
                );
                break;

            case RefData::ORG_TYPE_PARTNERSHIP:
            case RefData::ORG_TYPE_OTHER:
                $requiredVars = array(
                    'name' => isset($orgData['name'])
                );
                break;

            case RefData::ORG_TYPE_SOLE_TRADER:
                return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
        }

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get addresses status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateAddressesStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getAddressesStatus($applicationData)
    {
        $phoneNumber = false;
        $correspondenceAddress = isset($applicationData['licence']['correspondenceCd'])
            && !empty($applicationData['licence']['correspondenceCd']);
        $establishmentAddress = false;
        $skipEstablishmentAddress = false;

        $allowedLicTypes = array(
            RefData::LICENCE_TYPE_STANDARD_NATIONAL,
            RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        if (!in_array($applicationData['licenceType']['id'], $allowedLicTypes)) {
            $skipEstablishmentAddress = true;
            $establishmentAddress = true;
        }

        $corAdd = $applicationData['licence']['correspondenceCd'];

        // Must have a phone number
        if (is_array($corAdd['phoneContacts'])) {
            $number = array_shift($corAdd['phoneContacts']);
            $phoneNumber = !empty($number['phoneNumber']);
        }

        if (!$skipEstablishmentAddress
            && isset($applicationData['licence']['establishmentCd'])
            && !empty($applicationData['licence']['establishmentCd'])
        ) {
            $establishmentAddress = true;
        }

        $requiredVars = array(
            'phoneNumber' => $phoneNumber,
            'correspondenceAddress' => $correspondenceAddress,
            'establishmentAddress' => $establishmentAddress
        );

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get people status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdatePeopleStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getPeopleStatus($applicationData)
    {
        return $this->checkCompletion(
            array(
                !empty($applicationData['licence']['organisation']['organisationPersons'])
            )
        );
    }

    /**
     * Get Taxi Phv status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateTaxiPhvStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getTaxiPhvStatus($applicationData)
    {
        return $this->checkCompletion(
            array(
                !empty($applicationData['licence']['privateHireLicences'])
            )
        );
    }

    /**
     * Get operating centres status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateOperatingCentresStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getOperatingCentresStatus($applicationData)
    {
        if (count($applicationData['operatingCentres']) === 0) {
            return RefData::APPLICATION_COMPLETION_STATUS_INCOMPLETE;
        }

        $requiredVars = array(
            'totAuthSmallVehicles' => $applicationData['totAuthSmallVehicles'] !== null,
            'totAuthMediumVehicles' => $applicationData['totAuthMediumVehicles'] !== null,
            'totAuthLargeVehicles' => $applicationData['totAuthLargeVehicles'] !== null,
            'totAuthVehicles' => $applicationData['totAuthVehicles'] !== null,
            'totAuthTrailers' => $applicationData['totAuthTrailers'] !== null,
            'totCommunityLicences' => $applicationData['totCommunityLicences'] !== null,
        );

        if ($applicationData['goodsOrPsv']['id'] === RefData::LICENCE_CATEGORY_GOODS_VEHICLE) {
            unset($requiredVars['totAuthSmallVehicles']);
            unset($requiredVars['totAuthMediumVehicles']);
            unset($requiredVars['totAuthLargeVehicles']);
            unset($requiredVars['totCommunityLicences']);
        } else {
            unset($requiredVars['totAuthVehicles']);
            unset($requiredVars['totAuthTrailers']);

            $allowLargeVehicles = array(
                RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
            );

            $allowCommunityLicences = array(
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                RefData::LICENCE_TYPE_RESTRICTED
            );

            $licType = $applicationData['licenceType']['id'];

            if (!in_array($licType, $allowLargeVehicles)) {
                unset($requiredVars['totAuthLargeVehicles']);
            }

            if (!in_array($licType, $allowCommunityLicences)) {
                unset($requiredVars['totCommunityLicences']);
            }
        }

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get financial evidence status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateFinancialEvidenceStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getFinancialEvidenceStatus($applicationData)
    {
        return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
    }

    /**
     * Get Transport Managers status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateTransportManagersStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getTransportManagersStatus($applicationData)
    {
        $requiredTransportManager = [
            RefData::LICENCE_TYPE_STANDARD_NATIONAL,
            RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        // if licence type requires at least on Transport Manager
        if (in_array($applicationData['licenceType']['id'], $requiredTransportManager)) {
            // if no Transport Managers
            if (count($applicationData['transportManagers']) === 0) {
                return RefData::APPLICATION_COMPLETION_STATUS_INCOMPLETE;
            }
        }

        return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
    }

    /**
     * Get vehicles status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateVehiclesStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getVehiclesStatus($applicationData)
    {
        if ($applicationData['hasEnteredReg'] === 'N') {
            return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
        }

        $totalAuth = $applicationData['totAuthVehicles'];

        return $this->checkCompletion(
            array(
                (isset($applicationData['licence']['licenceVehicles'])
                    && !empty($applicationData['licence']['licenceVehicles'])),
                (is_numeric($totalAuth) && count($applicationData['licence']['licenceVehicles']) <= $totalAuth)
            )
        );
    }

    /**
     * Get vehicles psv status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateVehiclesPsvStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getVehiclesPsvStatus($applicationData)
    {
        if ($applicationData['hasEnteredReg'] === 'N') {
            return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
        }

        if (!isset($applicationData['licence']['licenceVehicles'])) {
            return RefData::APPLICATION_COMPLETION_STATUS_INCOMPLETE;
        }

        $psvTypes = [
            'small'  => RefData::PSV_TYPE_SMALL,
            'medium' => RefData::PSV_TYPE_MEDIUM,
            'large'  => RefData::PSV_TYPE_LARGE
        ];

        if ($applicationData['licenceType']['id'] === RefData::LICENCE_TYPE_RESTRICTED) {
            unset($psvTypes['large']);
        }

        foreach ($psvTypes as $type => $val) {
            /**
             * This loop looks *similar* to normal vehicles but it's inverted;
             * we want to bail as early as possible if things don't look right
             */
            $totalAuth = $applicationData['totAuth' . ucfirst($type) . 'Vehicles'];

            if ($totalAuth === null) {
                // bail early; a null (as opposed to a zero) means we haven't
                // answered this question, so how can we be complete?
                return RefData::APPLICATION_COMPLETION_STATUS_INCOMPLETE;
            }

            $totalVehicles = 0;

            foreach ($applicationData['licence']['licenceVehicles'] as $licenceVehicle) {
                if (isset($licenceVehicle['vehicle']['psvType']['id'])
                    && $licenceVehicle['vehicle']['psvType']['id'] === $val
                ) {
                    $totalVehicles++;
                }
            }

            if ($totalVehicles > $totalAuth) {
                return RefData::APPLICATION_COMPLETION_STATUS_INCOMPLETE;
            }
        }

        // if we made it here, life must be good
        return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
    }

    /**
     * Get vehicle declarations status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateVehiclesDeclarationsStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getVehiclesDeclarationsStatus($applicationData)
    {
        $requiredVars = array(
            // NineOrMore
            'psvNoSmallVhlConfirmation' => $this->isYnValue($applicationData['psvNoSmallVhlConfirmation']),
            // SmallVehiclesIntention
            'psvOperateSmallVhl' => $this->isYnValue($applicationData['psvOperateSmallVhl']),
            'psvSmallVhlNotes' => !empty($applicationData['psvSmallVhlNotes']),
            'psvSmallVhlConfirmation' => $this->isYnValue($applicationData['psvSmallVhlConfirmation']),
            // limousinesNoveltyVehicles
            'psvLimousines' => $this->isYnValue($applicationData['psvLimousines']),
            'psvNoLimousineConfirmation' => $this->isYnValue($applicationData['psvNoLimousineConfirmation']),
            'psvOnlyLimousinesConfirmation' => $this->isYnValue($applicationData['psvOnlyLimousinesConfirmation'])
        );

        if (!isset($applicationData['licence']['trafficArea']['isScotland'])) {
            $applicationData['licence']['trafficArea']['isScotland'] = false;
        }

        if (empty($applicationData['totAuthSmallVehicles'])) {
            unset($requiredVars['psvOperateSmallVhl']);
            unset($requiredVars['psvSmallVhlNotes']);
            unset($requiredVars['psvSmallVhlConfirmation']);
        } else {
            unset($requiredVars['psvNoSmallVhlConfirmation']);

            if (empty($applicationData['totAuthMediumVehicles']) && empty($applicationData['totAuthLargeVehicles'])) {
                unset($requiredVars['psvOnlyLimousinesConfirmation']);
            }

            if ($applicationData['licence']['trafficArea']['isScotland']) {
                unset($requiredVars['psvOperateSmallVhl']);
                unset($requiredVars['psvSmallVhlNotes']);
            }

            if ($applicationData['psvOperateSmallVhl'] === 'N') {
                unset($requiredVars['psvSmallVhlNotes']);
            }
        }

        $total = array_sum(
            array(
                (int)$applicationData['totAuthSmallVehicles'],
                (int)$applicationData['totAuthMediumVehicles'],
                (int)$applicationData['totAuthLargeVehicles']
            )
        );

        $requiredVars[] = $this->isAtLeast1($total);

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get safety status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateSafetyStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getSafetyStatus($applicationData)
    {
        $requiredVars = array(
            $this->isAtLeast1($applicationData['licence']['safetyInsVehicles']),
            $this->isYnValue($applicationData['licence']['safetyInsVaries']),
            !empty($applicationData['licence']['tachographIns']['id']),
            $this->isAtLeast1(count($applicationData['licence']['workshops'])),
            $this->isYnValue($applicationData['safetyConfirmation'])
        );

        if ($applicationData['licence']['tachographIns']['id'] === 'tach_external') {
            $requiredVars[] = !empty($applicationData['licence']['tachographInsName']);
        }

        if ($applicationData['goodsOrPsv']['id'] === RefData::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $requiredVars[] = $applicationData['licence']['safetyInsTrailers'] !== '';
        }

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get conditions undertakings status
     *
     * @NOTE This functionality has been replicated in the API
     * [ApplicationCompletion/UpdateConditionsUndertakingsStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getConditionsUndertakingsStatus($applicationData)
    {
        return RefData::APPLICATION_COMPLETION_STATUS_COMPLETE;
    }

    /**
     * Get financial history status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateFinancialHistoryStatus]
     *
     * @param array $applicationData Application Data
     *
     * @return int
     */
    protected function getFinancialHistoryStatus($applicationData)
    {
        $ynVars = array(
            'bankrupt',
            'liquidation',
            'receivership',
            'administration',
            'disqualified'
        );

        $requiredVars = array();

        foreach ($ynVars as $var) {
            $requiredVars[] = $this->isYnValue($applicationData[$var]);
        }

        foreach ($ynVars as $var) {
            if ($applicationData[$var] === 'Y') {
                $requiredVars[] = (strlen(preg_replace('/\s+/', '', $applicationData['insolvencyDetails'])) >= 150);

                break;
            }
        }

        $requiredVars[] = $applicationData['insolvencyConfirmation'] === 'Y';

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get licence history status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateLicenceHistoryStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getLicenceHistoryStatus($applicationData)
    {
        $sections = array(
            'prevHasLicence',
            'prevHadLicence',
            'prevBeenRefused',
            'prevBeenRevoked',
            'prevBeenDisqualifiedTc',
            'prevBeenAtPi',
            'prevPurchasedAssets'
        );

        $requiredVars = array();

        $filter = $this->getServiceLocator()->get('Helper\String');

        // We must have values for each radio
        foreach ($sections as $section) {
            $licenceType = $filter->camelToUnderscore($section);

            $requiredVars[] = $this->isYnValue($applicationData[$section]);

            // If the radio is YES, then we must have at least 1 licence of that type
            if ($applicationData[$section] === 'Y') {
                $hasLicences = false;

                foreach ($applicationData['otherLicences'] as $licence) {
                    if ($licence['previousLicenceType']['id'] === $licenceType) {
                        $hasLicences = true;
                    }
                }

                $requiredVars[] = $hasLicences;
            }
        }

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get conviction penalties status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateConvictionsPenaltiesStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getConvictionsPenaltiesStatus($applicationData)
    {
        $requiredVars = array(
            $this->isYnValue($applicationData['prevConviction']),
            $applicationData['convictionsConfirmation'] === 'Y'
        );

        if ($applicationData['prevConviction'] === 'Y') {
            $requiredVars[] = $this->isAtLeast1(count($applicationData['previousConvictions']));
        }

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get undertakings status
     *
     * @NOTE This functionality has been replicated in the API [ApplicationCompletion/UpdateUndertakingsStatus]
     *
     * @param array $applicationData
     * @return int
     */
    protected function getUndertakingsStatus($applicationData)
    {
        $requiredVars = array(
            $applicationData['declarationConfirmation'] === 'Y'
        );

        return $this->checkCompletion($requiredVars);
    }
}
