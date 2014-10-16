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
        $data = $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'GET', array('application' => $applicationId));

        if ($data['Count'] < 1) {
            throw new \Exception('Completions status not found');
        }

        if ($data['Count'] > 1) {
            throw new \Exception('Too many completion statuses found');
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

            if ($section === $currentSection || $completionStatus[$property] > self::STATUS_NOT_STARTED) {
                $completionStatus[$property] = $this->$method($applicationData);
            }
        }

        $this->save($completionStatus);
    }

    /**
     * Get type of licence status
     *
     * @param array $applicationData
     * @return int
     */
    public function getTypeOfLicenceStatus($applicationData)
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

    private function checkCompletion(array $properties = array())
    {
        $completeCount = 0;

        foreach ($properties as $value) {
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
     * Get business type status
     *
     * @param array $applicationData
     * @return int
     */
    public function getBusinessTypeStatus($applicationData)
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
    public function getBusinessDetailsStatus($applicationData)
    {
        if (!isset($applicationData['licence']['organisation']['type']['id'])) {
            return self::STATUS_NOT_STARTED;
        }

        $orgData = $applicationData['licence']['organisation'];

        /**
         * Selectively add required fields based on the org type
         */
        switch ($orgData['type']['id']) {
            case OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY:
            case OrganisationEntityService::ORG_TYPE_LLP:
                $registeredAddress = false;

                foreach ($orgData['contactDetails'] as $contactDetail) {
                    if ($contactDetail['contactType']['id'] == ContactDetailsEntityService::CONTACT_TYPE_REGISTERED) {
                        $registeredAddress = true;
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
                $requiredVars = array();
                break;
        }

        return $this->checkCompletion($requiredVars);
    }

    /**
     * Get addresses status
     *
     * @param array $applicationData
     * @return int
     */
    public function getAddressesStatus($applicationData)
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
    public function getPeopleStatus($applicationData)
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
    public function getTaxiPhvStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get operatings centres status
     *
     * @param array $applicationData
     * @return int
     */
    public function getOperatingCentresStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get financial evidence status
     *
     * @param array $applicationData
     * @return int
     */
    public function getFinancialEvidenceStatus($applicationData)
    {
        return self::STATUS_COMPLETE;
    }

    /**
     * Get transport managers status
     *
     * @param array $applicationData
     * @return int
     */
    public function getTransportManagersStatus($applicationData)
    {
        return self::STATUS_COMPLETE;
    }

    /**
     * Get vehicles status
     *
     * @param array $applicationData
     * @return int
     */
    public function getVehiclesStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get vehicles psv status
     *
     * @param array $applicationData
     * @return int
     */
    public function getVehiclesPsvStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get vehicle declarations status
     *
     * @param array $applicationData
     * @return int
     */
    public function getVehiclesDeclarationsStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get discs status
     *
     * @param array $applicationData
     * @return int
     */
    public function getDiscsStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get community licences status
     *
     * @param array $applicationData
     * @return int
     */
    public function getCommunityLicencesStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get safety status
     *
     * @param array $applicationData
     * @return int
     */
    public function getSafetyStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get conditions undertakings status
     *
     * @param array $applicationData
     * @return int
     */
    public function getConditionsUndertakingsStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get financial history status
     *
     * @param array $applicationData
     * @return int
     */
    public function getFinancialHistoryStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get licence history status
     *
     * @param array $applicationData
     * @return int
     */
    public function getLicenceHistoryStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get conviction penalties status
     *
     * @param array $applicationData
     * @return int
     */
    public function getConvictionsPenaltiesStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }
}
