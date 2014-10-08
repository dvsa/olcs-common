<?php

/**
 * Application Completion Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\Data\SectionConfig;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Application Completion Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationCompletionService extends AbstractEntityService
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
     * Update completion statuses
     *
     * @param int $applicationId
     */
    public function updateCompletionStatuses($applicationId)
    {
        $data = $this->getHelperService('RestHelper')
            ->makeRestCall($this->entity, 'GET', array('application' => $applicationId));

        if ($data['Count'] < 1) {
            throw new \Exception('Completions status not found');
        }

        if ($data['Count'] > 1) {
            throw new \Exception('Too many completion statuses found');
        }

        $completionStatus = $data['Results'][0];
        $completionStatus['application'] = $applicationId;

        $sectionConfig = new SectionConfig();
        $sections = $sectionConfig->getAllReferences();

        $applicationData = $this->getEntityService('Application')->getDataForCompletionStatus($applicationId);

        $filter = new UnderscoreToCamelCase();

        foreach ($sections as $section) {
            $section = $filter->filter($section);

            $method = 'get' . $section . 'Status';

            $completionStatus[lcfirst($section) . 'Status'] = $this->$method($applicationData);
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

        $requiredVars = array(
            'niFlag' => $licence['niFlag'],
            'goodsOrPsv' => isset($licence['goodsOrPsv']['id']) ? $licence['goodsOrPsv']['id'] : null,
            'licenceType' => isset($licence['licenceType']['id']) ? $licence['licenceType']['id'] : null,
        );

        $countValues = 0;

        foreach ($requiredVars as $value) {
            if ($value !== null) {
                $countValues++;
            }
        }

        if ($countValues === count($requiredVars)) {
            return self::STATUS_COMPLETE;
        }

        if ($countValues > 0) {
            return self::STATUS_INCOMPLETE;
        }

        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get business type status
     *
     * @param array $applicationData
     * @return int
     */
    public function getBusinessTypeStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get business details status
     *
     * @param array $applicationData
     * @return int
     */
    public function getBusinessDetailsStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get addresses status
     *
     * @param array $applicationData
     * @return int
     */
    public function getAddressesStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get people status
     *
     * @param array $applicationData
     * @return int
     */
    public function getPeopleStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
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
        return self::STATUS_NOT_STARTED;
    }

    /**
     * Get transport managers status
     *
     * @param array $applicationData
     * @return int
     */
    public function getTransportManagersStatus($applicationData)
    {
        return self::STATUS_NOT_STARTED;
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
