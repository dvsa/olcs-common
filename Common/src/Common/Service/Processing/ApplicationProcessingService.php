<?php

/**
 * Application Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Common\RefData;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Service\Data\FeeTypeDataService;

/**
 * Application Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @NOTE This functionality has been replicated in the API [Application/CreateApplicationFee || Fee/CreateFee]
     */
    public function createFee($applicationId, $licenceId, $feeTypeName, $taskId = null)
    {
        $feeType = $this->getFeeTypeForApplication($applicationId, $feeTypeName);
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $feeData = array(
            'amount' => (float)($feeType['fixedValue'] === '0.00' ? $feeType['fiveYearValue'] : $feeType['fixedValue']),
            'application' => $applicationId,
            'licence' => $licenceId,
            'invoicedDate' => $date,
            'feeType' => $feeType['id'],
            'description' => $feeType['description'] . ' for application ' . $applicationId,
            'feeStatus' => RefData::FEE_STATUS_OUTSTANDING,
            'task' => $taskId
        );

        $this->getServiceLocator()->get('Entity\Fee')->save($feeData);
    }

    public function getInterimFee($applicationId)
    {
        return $this->getFeeForApplicationByType($applicationId, FeeTypeDataService::FEE_TYPE_GRANTINT);
    }

    public function getApplicationFee($applicationId)
    {
        $applicationType = $this->getServiceLocator()->get('Entity\Application')
            ->getApplicationType($applicationId);

        $feeType =($applicationType == RefData::APPLICATION_TYPE_VARIATION)
            ? FeeTypeDataService::FEE_TYPE_VAR
            : FeeTypeDataService::FEE_TYPE_APP;

        return $this->getFeeForApplicationByType($applicationId, $feeType);
    }

    public function voidCommunityLicencesForLicence($licenceId)
    {
        $licences = $this->getServiceLocator()->get('Entity\Licence')
            ->getCommunityLicencesByLicenceId($licenceId);

        $data = [
            'status' => RefData::COMMUNITY_LICENCE_STATUS_VOID,
            'expiredDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(\DateTime::W3C),
        ];
        $dataToVoid = [];
        foreach ($licences as $licence) {
            if ($licence['expiredDate'] === null) {
                $dataToVoid[] = array_merge($licence, $data);
            }
        }

        $this->getServiceLocator()->get('Entity\CommunityLic')->multiUpdate($dataToVoid);
        $this->getServiceLocator()->get('Entity\Licence')->updateCommunityLicencesCount($licenceId);
    }

    /**
     * Expire Community Licences on a Licence
     *
     * @param int $licenceId Licence ID
     */
    public function expireCommunityLicencesForLicence($licenceId)
    {
        $communityLicences = $this->getServiceLocator()->get('Entity\Licence')
            ->getCommunityLicencesByLicenceId($licenceId);

        $data = [
            'status' => RefData::COMMUNITY_LICENCE_STATUS_EXPIRED,
            'expiredDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(\DateTime::W3C),
        ];

        // only expire community licences that have these statuses
        $statusesToExpire = [
            RefData::COMMUNITY_LICENCE_STATUS_PENDING,
            RefData::COMMUNITY_LICENCE_STATUS_ACTIVE,
            RefData::COMMUNITY_LICENCE_STATUS_SUSPENDED
        ];

        $dataToExpire = [];
        foreach ($communityLicences as $communityLicence) {
            if (in_array($communityLicence['status']['id'], $statusesToExpire)) {
                $dataToExpire[] = array_merge($communityLicence, $data);
            }
        }

        $this->getServiceLocator()->get('Entity\CommunityLic')->multiUpdate($dataToExpire);
        $this->getServiceLocator()->get('Entity\Licence')->updateCommunityLicencesCount($licenceId);
    }

    /**
     * @NOTE This functionality has been replicated in the API
     * [Application/CreateApplicationFee || Repository/FeeType->getLatest()]
     *
     * Get the latest fee type for a application
     *
     * @param int $applicationId
     * @param string $feeType
     * @return int
     */
    public function getFeeTypeForApplication($applicationId, $feeType)
    {
        $applicationService = $this->getServiceLocator()->get('Entity\Application');

        $data = $applicationService->getTypeOfLicenceData($applicationId);
        $date = $applicationService->getApplicationDate($applicationId);

        return $this->getServiceLocator()->get('Data\FeeType')->getLatest(
            $feeType,
            $data['goodsOrPsv'],
            $data['licenceType'],
            $date,
            ($data['niFlag'] === 'Y') ? RefData::NORTHERN_IRELAND_TRAFFIC_AREA_CODE : null
        );
    }

    private function getFeeForApplicationByType($applicationId, $feeType)
    {
        $feeTypeData = $this->getFeeTypeForApplication(
            $applicationId,
            $feeType
        );

        return $this->getServiceLocator()->get('Entity\Fee')->getLatestFeeByTypeStatusesAndApplicationId(
            $feeTypeData['id'],
            [RefData::FEE_STATUS_OUTSTANDING],
            $applicationId
        );
    }
}
