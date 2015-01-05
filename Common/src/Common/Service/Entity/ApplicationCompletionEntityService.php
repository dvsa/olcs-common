<?php

/**
 * Application Completion Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\Data\SectionConfig;
use Common\Service\Entity\OrganisationEntityService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\VehicleEntityService;

/**
 * Application Completion Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationCompletionEntityService extends AbstractEntityService
{
    const STATUS_NOT_STARTED = 0;
    const STATUS_INCOMPLETE = 1;
    const STATUS_COMPLETE = 2;

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
            throw new Exceptions\UnexpectedResponseException('Completions status not found');
        }

        if ($data['Count'] > 1) {
            throw new Exceptions\UnexpectedResponseException('Too many completion statuses found');
        }

        return $data['Results'][0];
    }

    /**
     * Update completion statuses
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

            if ($section === $currentSection || $completionStatus[$property] != self::STATUS_NOT_STARTED) {
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
            return self::STATUS_COMPLETE;
        }

        return self::STATUS_INCOMPLETE;
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
     * Get type of licence status
     *
     * @param array $applicationData
     * @return int
     */
    private function getTypeOfLicenceStatus($applicationData)
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
     * @param array $applicationData
     * @return int
     */
    private function getBusinessTypeStatus($applicationData)
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
     * @param array $applicationData
     * @return int
     */
    private function getBusinessDetailsStatus($applicationData)
    {
        if (!isset($applicationData['licence']['organisation']['type']['id'])) {
            return self::STATUS_INCOMPLETE;
        }

        $orgData = $applicationData['licence']['organisation'];

        /**
         * Selectively add required fields based on the org type
         */
        switch ($orgData['type']['id']) {
            case OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY:
            case OrganisationEntityService::ORG_TYPE_LLP:

                $registeredAddress = !empty($orgData['contactDetails']);

                $requiredVars = array(
                    'name' => isset($orgData['name']),
                    'company' => isset($orgData['companyOrLlpNo']),
                    'registeredAddress' => $registeredAddress
                );
                break;

            case OrganisationEntityService::ORG_TYPE_PARTNERSHIP:
            case OrganisationEntityService::ORG_TYPE_OTHER:
                $requiredVars = array(
                    'name' => isset($orgData['name'])
                );
                break;

            case OrganisationEntityService::ORG_TYPE_SOLE_TRADER:
                return self::STATUS_COMPLETE;
        }

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get addresses status
     *
     * @param array $applicationData
     * @return int
     */
    private function getAddressesStatus($applicationData)
    {
        $phoneNumber = false;
        $correspondenceAddress = isset($applicationData['licence']['correspondenceCd'])
            && !empty($applicationData['licence']['correspondenceCd']);
        $establishmentAddress = false;
        $skipEstablishmentAddress = false;

        $allowedLicTypes = array(
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        if (!in_array($applicationData['licenceType']['id'], $allowedLicTypes)) {
            $skipEstablishmentAddress = true;
            $establishmentAddress = true;
        }

        $corAdd = $applicationData['licence']['correspondenceCd'];

        if (isset($corAdd['phoneContacts'][0])) {
            $phoneNumber = !empty($corAdd['phoneContacts'][0]['phoneNumber']);
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
     * @param array $applicationData
     * @return int
     */
    private function getPeopleStatus($applicationData)
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
     * @param array $applicationData
     * @return int
     */
    private function getTaxiPhvStatus($applicationData)
    {
        return $this->checkCompletion(
            array(
                !empty($applicationData['licence']['privateHireLicences'])
            )
        );
    }

    /**
     * Get operatings centres status
     *
     * @param array $applicationData
     * @return int
     */
    private function getOperatingCentresStatus($applicationData)
    {
        if (count($applicationData['operatingCentres']) === 0) {
            return self::STATUS_INCOMPLETE;
        }

        $requiredVars = array(
            'totAuthSmallVehicles' => $applicationData['totAuthSmallVehicles'] !== null,
            'totAuthMediumVehicles' => $applicationData['totAuthMediumVehicles'] !== null,
            'totAuthLargeVehicles' => $applicationData['totAuthLargeVehicles'] !== null,
            'totAuthVehicles' => $applicationData['totAuthVehicles'] !== null,
            'totAuthTrailers' => $applicationData['totAuthTrailers'] !== null,
            'totCommunityLicences' => $applicationData['totCommunityLicences'] !== null,
        );

        if ($applicationData['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {

            unset($requiredVars['totAuthSmallVehicles']);
            unset($requiredVars['totAuthMediumVehicles']);
            unset($requiredVars['totAuthLargeVehicles']);
            unset($requiredVars['totCommunityLicences']);

        } else {

            unset($requiredVars['totAuthVehicles']);
            unset($requiredVars['totAuthTrailers']);

            $allowLargeVehicles = array(
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            );

            $allowCommunityLicences = array(
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                LicenceEntityService::LICENCE_TYPE_RESTRICTED
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
     * @param array $applicationData
     * @return int
     */
    private function getFinancialEvidenceStatus($applicationData)
    {
        return self::STATUS_COMPLETE;
    }

    /**
     * Get transport managers status
     *
     * @param array $applicationData
     * @return int
     */
    private function getTransportManagersStatus($applicationData)
    {
        return self::STATUS_COMPLETE;
    }

    /**
     * Get vehicles status
     *
     * @param array $applicationData
     * @return int
     */
    private function getVehiclesStatus($applicationData)
    {
        if ($applicationData['hasEnteredReg'] === 'N') {
            return self::STATUS_COMPLETE;
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
     * @param array $applicationData
     * @return int
     */
    private function getVehiclesPsvStatus($applicationData)
    {
        if ($applicationData['hasEnteredReg'] === 'N') {
            return self::STATUS_COMPLETE;
        }

        if (!isset($applicationData['licence']['licenceVehicles'])) {
            return self::STATUS_INCOMPLETE;
        }

        $psvTypes = [
            'small'  => VehicleEntityService::PSV_TYPE_SMALL,
            'medium' => VehicleEntityService::PSV_TYPE_MEDIUM,
            'large'  => VehicleEntityService::PSV_TYPE_LARGE
        ];

        if ($applicationData['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED) {
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
                return self::STATUS_INCOMPLETE;
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
                return self::STATUS_INCOMPLETE;
            }
        }

        // if we made it here, life must be good
        return self::STATUS_COMPLETE;
    }

    /**
     * Get vehicle declarations status
     *
     * @param array $applicationData
     * @return int
     */
    private function getVehiclesDeclarationsStatus($applicationData)
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
     * @param array $applicationData
     * @return int
     */
    private function getSafetyStatus($applicationData)
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

        if ($applicationData['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $requiredVars[] = $this->isAtLeast1($applicationData['licence']['safetyInsTrailers']);
        }

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get conditions undertakings status
     *
     * @param array $applicationData
     * @return int
     */
    private function getConditionsUndertakingsStatus($applicationData)
    {
        return self::STATUS_COMPLETE;
    }

    /**
     * Get financial history status
     *
     * @param array $applicationData
     * @return int
     */
    private function getFinancialHistoryStatus($applicationData)
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
                $requiredVars[] = !empty($applicationData['insolvencyDetails'])
                    && strlen($applicationData['insolvencyDetails']) >= 200;
                break;
            }
        }

        $requiredVars[] = $applicationData['insolvencyConfirmation'] === 'Y';

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get licence history status
     *
     * @param array $applicationData
     * @return int
     */
    private function getLicenceHistoryStatus($applicationData)
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

                foreach ($applicationData['previousLicences'] as $licence) {

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
     * @param array $applicationData
     * @return int
     */
    private function getConvictionsPenaltiesStatus($applicationData)
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
     * @param array $applicationData
     * @return int
     */
    private function getUndertakingsStatus($applicationData)
    {
        return self::STATUS_COMPLETE;
    }
}
