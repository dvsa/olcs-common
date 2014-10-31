<?php

/**
 * Application Completion Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\Data\SectionConfig;
use Common\Service\Entity\OrganisationEntityService;
use Common\Service\Entity\ContactDetailsEntityService;
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
            // some values can legitimately be set to zero and still be valid, so empty won't do
            if ($value !== null) {
                $completeCount++;
            }
        }

        if ($completeCount === count($properties)) {
            return self::STATUS_COMPLETE;
        }

        return self::STATUS_INCOMPLETE;
    }

    /**
     * Get type of licence status
     *
     * @param array $applicationData
     * @return int
     */
    private function getTypeOfLicenceStatus($applicationData)
    {
        $licence = $applicationData['licence'];

        return $this->checkCompletion(
            array(
                'niFlag' => $licence['niFlag'],
                'goodsOrPsv' => isset($licence['goodsOrPsv']['id']) ? $licence['goodsOrPsv']['id'] : null,
                'licenceType' => isset($licence['licenceType']['id']) ? $licence['licenceType']['id'] : null,
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
        return empty($applicationData['licence']['organisation']['type']['id'])
            ? self::STATUS_NOT_STARTED
            : self::STATUS_COMPLETE;
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
                $registeredAddress = null;

                foreach ($orgData['contactDetails'] as $contactDetail) {
                    if ($contactDetail['contactType']['id'] === ContactDetailsEntityService::CONTACT_TYPE_REGISTERED) {
                        $registeredAddress =  true;
                        break;
                    }
                }

                $requiredVars = array(
                    'name' => isset($orgData['name']) ? $orgData['name'] : null,
                    'company' => isset($orgData['companyOrLlpNo']) ? $orgData['companyOrLlpNo'] : null,
                    'registeredAddress' => $registeredAddress
                );
                break;

            case OrganisationEntityService::ORG_TYPE_PARTNERSHIP:
            case OrganisationEntityService::ORG_TYPE_OTHER:
                $requiredVars = array(
                    'name' => isset($orgData['name']) ? $orgData['name'] : null
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
        $phoneNumber = null;
        $correspondenceAddress = null;
        $establishmentAddress = null;
        $skipEstablishmentAddress = false;

        $contactDetails = array_merge(
            $applicationData['licence']['contactDetails'],
            $applicationData['licence']['organisation']['contactDetails']
        );

        $allowedLicTypes = array(
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        if (!in_array($applicationData['licence']['licenceType']['id'], $allowedLicTypes)) {
            $skipEstablishmentAddress = true;
            $establishmentAddress = true;
        }

        foreach ($contactDetails as $contactDetail) {
            if (isset($contactDetail['phoneContacts'][0])) {
                $phoneNumber = $contactDetail['phoneContacts'][0]['phoneNumber'];
            }

            if (isset($contactDetail['contactType']['id'])
                && $contactDetail['contactType']['id'] === ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE
            ) {
                $correspondenceAddress = true;

                if ($skipEstablishmentAddress) {
                    break;
                }
            }

            if (isset($contactDetail['contactType']['id'])
                && $contactDetail['contactType']['id'] === ContactDetailsEntityService::CONTACT_TYPE_ESTABLISHMENT) {
                $establishmentAddress = true;
                break;
            }
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
        return count($applicationData['licence']['organisation']['organisationPersons'])
            ? self::STATUS_COMPLETE
            : self::STATUS_INCOMPLETE;
    }

    /**
     * Get Taxi Phv status
     *
     * @param array $applicationData
     * @return int
     */
    private function getTaxiPhvStatus($applicationData)
    {
        if (!empty($applicationData['licence']['privateHireLicences'])) {
            return self::STATUS_COMPLETE;
        }

        return self::STATUS_INCOMPLETE;
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
            'totAuthSmallVehicles' => $applicationData['totAuthSmallVehicles'],
            'totAuthMediumVehicles' => $applicationData['totAuthMediumVehicles'],
            'totAuthLargeVehicles' => $applicationData['totAuthLargeVehicles'],
            'totAuthVehicles' => $applicationData['totAuthVehicles'],
            'totAuthTrailers' => $applicationData['totAuthTrailers'],
            'totCommunityLicences' => $applicationData['totCommunityLicences'],
        );

        if ($applicationData['licence']['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {

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

            $licType = $applicationData['licence']['licenceType']['id'];

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
        $totalAuth = $applicationData['totAuthVehicles'];

        if (isset($applicationData['licence']['licenceVehicles'])
            && !empty($applicationData['licence']['licenceVehicles'])
            && (is_numeric($totalAuth) && count($applicationData['licence']['licenceVehicles']) <= $totalAuth)
        ) {
            return self::STATUS_COMPLETE;
        }

        return self::STATUS_INCOMPLETE;
    }

    /**
     * Get vehicles psv status
     *
     * @param array $applicationData
     * @return int
     */
    private function getVehiclesPsvStatus($applicationData)
    {
        if (!isset($applicationData['licence']['licenceVehicles'])) {
            return self::STATUS_INCOMPLETE;
        }

        $psvTypes = [
            'small'  => VehicleEntityService::PSV_TYPE_SMALL,
            'medium' => VehicleEntityService::PSV_TYPE_MEDIUM,
            'large'  => VehicleEntityService::PSV_TYPE_LARGE
        ];

        foreach ($psvTypes as $type => $val) {
            /*
             * This loop looks *similar* to normal vehicles but it's inverted;
             * we want to bail as early as possible if things don't look right
             */
            $totalAuth = $applicationData['totAuth' . ucfirst($type). 'Vehicles'];

            $totalVehicles = 0;

            foreach ($applicationData['licence']['licenceVehicles'] as $vehicle) {
                if (isset($vehicle['psvType']['id']) && $vehicle['psvType']['id'] === $val) {
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
            'psvNoSmallVhlConfirmation' => $applicationData['psvNoSmallVhlConfirmation'],
            // SmallVehiclesIntention
            'psvOperateSmallVhl' => $applicationData['psvOperateSmallVhl'],
            'psvSmallVhlNotes' => $applicationData['psvSmallVhlNotes'],
            'psvSmallVhlConfirmation' => $applicationData['psvSmallVhlConfirmation'],
            // limousinesNoveltyVehicles
            'psvLimousines' => $applicationData['psvLimousines'],
            'psvNoLimousineConfirmation' => $applicationData['psvNoLimousineConfirmation'],
            'psvOnlyLimousinesConfirmation' => $applicationData['psvOnlyLimousinesConfirmation']
        );

        if (!isset($applicationData['licence']['trafficArea']['isScottishRules'])) {
            $applicationData['licence']['trafficArea']['isScottishRules'] = false;
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

            if ($applicationData['licence']['trafficArea']['isScottishRules']) {
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

        $requiredVars[] = array(
            'total' => ($total !== 0 ? $total : null)
        );

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
            $applicationData['licence']['safetyInsVehicles'],
            $applicationData['licence']['safetyInsVaries'],
            $applicationData['licence']['tachographIns']['id'],
            count($applicationData['licence']['workshops']),
            $applicationData['safetyConfirmation']
        );

        if ($applicationData['licence']['tachographIns']['id'] === 'tach_external') {
            $requiredVars[] = $applicationData['licence']['tachographInsName'];
        }

        if ($applicationData['licence']['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $requiredVars[] = $applicationData['licence']['safetyInsTrailers'];
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
        // @todo need to do this
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
        $requiredVars = array(
            $applicationData['bankrupt'],
            $applicationData['liquidation'],
            $applicationData['receivership'],
            $applicationData['administration'],
            $applicationData['disqualified']
        );

        foreach ($requiredVars as $var) {
            if ($var === 'Y') {
                $requiredVars[] = $applicationData['insolvencyDetails'];
                break;
            }
        }

        $requiredVars[] = $applicationData['insolvencyConfirmation'];

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

            $requiredVars[] = $applicationData[$section];

            // If the radio is YES, then we must have at least 1 licence of that type
            if ($applicationData[$section] === 'Y') {

                $hasLicences = null;

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
            $applicationData['prevConviction'],
            $applicationData['convictionsConfirmation']
        );

        if ($applicationData['prevConviction'] === 'Y') {
            $requiredVars[] = count($applicationData['previousConvictions']);
        }

        return $this->checkCompletion($requiredVars);
    }
}
