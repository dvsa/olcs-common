<?php

/**
 * Application Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Data\CategoryDataService;
use Common\Service\Entity\FeeEntityService;
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
     * Cache validating data for applications
     *
     * @var array
     */
    protected $applicationValidatingData = array();

    public function validateApplication($id)
    {
        $licenceId = $this->getLicenceId($id);

        $this->setApplicationStatus($id, ApplicationEntityService::APPLICATION_STATUS_VALID);

        $this->copyApplicationDataToLicence($id, $licenceId);

        $this->processApplicationOperatingCentres($id, $licenceId);

        $category = $this->getServiceLocator()->get('Entity\Application')->getCategory($id);

        $this->createDiscRecords($licenceId, $category);

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addSuccessMessage('licence-valid-confirmation');
    }

    public function processGrantApplication($id)
    {
        $applicationService = $this->getServiceLocator()->get('Entity\Application');

        $licenceId = $applicationService->getLicenceIdForApplication($id);

        $category = $applicationService->getCategory($id);

        if ($category === LicenceEntityService::LICENCE_CATEGORY_PSV) {
            $this->processGrantPsvApplication($id, $licenceId);
        } else {
            $this->processGrantGoodsApplication($id, $licenceId);
        }
    }

    protected function processGrantPsvApplication($id, $licenceId)
    {
        $appStatus = ApplicationEntityService::APPLICATION_STATUS_VALID;
        $licStatus = LicenceEntityService::LICENCE_STATUS_VALID;

        $this->grantApplication($id, $appStatus);
        $this->grantLicence($licenceId, $licStatus);

        $this->copyApplicationDataToLicence($id, $licenceId);

        $dataForValidating = $this->getApplicationDataForValidating($id);

        if ($dataForValidating['licenceType'] !== LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            $this->processApplicationOperatingCentres($id, $licenceId);
        }

        $this->createDiscRecords($licenceId, LicenceEntityService::LICENCE_CATEGORY_PSV);
    }

    protected function processGrantGoodsApplication($id, $licenceId)
    {
        $this->grantApplication($id);
        $this->grantLicence($licenceId);

        $taskId = $this->createGrantTask($id, $licenceId);
        $this->createGrantFee($id, $licenceId, $taskId);
    }

    public function processUnGrantApplication($id)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($id);

        $this->undoGrantApplication($id);
        $this->undoGrantLicence($licenceId);

        $this->cancelFees($licenceId);
        $this->closeGrantTask($id, $licenceId);
    }

    protected function cancelFees($licenceId)
    {
        $this->getServiceLocator()->get('Entity\Fee')->cancelForLicence($licenceId);
    }

    protected function closeGrantTask($id, $licenceId)
    {
        $this->getServiceLocator()->get('Entity\Task')->closeByQuery(
            array(
                'category' => CategoryDataService::CATEGORY_APPLICATION,
                'taskSubCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE,
                'licence' => $licenceId,
                'application' => $id
            )
        );
    }

    protected function undoGrantApplication($id)
    {
        $status = ApplicationEntityService::APPLICATION_STATUS_UNDER_CONSIDERATION;

        $this->updateStatusAndDate($id, $status, 'Application', null);
    }

    protected function undoGrantLicence($id)
    {
        $status = LicenceEntityService::LICENCE_STATUS_UNDER_CONSIDERATION;

        $this->updateStatusAndDate($id, $status, 'Licence', null);
    }

    protected function grantApplication($id, $status = ApplicationEntityService::APPLICATION_STATUS_GRANTED)
    {
        $this->updateStatusAndDate($id, $status, 'Application');
    }

    protected function grantLicence($id, $status = LicenceEntityService::LICENCE_STATUS_GRANTED)
    {
        $this->updateStatusAndDate($id, $status, 'Licence');
    }

    protected function updateStatusAndDate($id, $status, $which, $grantedToday = true)
    {
        if ($grantedToday) {
            $grantedDate = $this->getServiceLocator()->get('Helper\Date')->getDate();
        } else {
            $grantedDate = $grantedToday;
        }

        $data = array(
            'status' => $status,
            'grantedDate' => $grantedDate
        );

        $this->getServiceLocator()->get('Entity\\' . $which)->forceUpdate($id, $data);
    }

    protected function createGrantTask($id, $licenceId)
    {
        $user = $this->getServiceLocator()->get('Entity\User')->getCurrentUser();
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $data = array(
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'taskSubCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE,
            'description' => 'Grant fee due',
            'actionDate' => $date,
            'assignedToUser' => $user['id'],
            'assignedToTeam' => $user['team']['id'],
            'isClosed' => 'N',
            'urgent' => 'N',
            'application' => $id,
            'licence' => $licenceId,
        );

        $saved = $this->getServiceLocator()->get('Entity\Task')->save($data);

        return $saved['id'];
    }

    protected function createGrantFee($applicationId, $licenceId, $taskId)
    {
        $feeType = $this->getFeeTypeForLicence($applicationId, $licenceId);
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $feeData = array(
            'amount' => (float)($feeType['fixedValue'] === '0.00' ? $feeType['fiveYearValue'] : $feeType['fixedValue']),
            'application' => $applicationId,
            'licence' => $licenceId,
            'invoicedDate' => $date,
            'feeType' => $feeType['id'],
            'description' => $feeType['description'] . ' for application ' . $applicationId,
            'feeStatus' => FeeEntityService::STATUS_OUTSTANDING,
            'task' => $taskId
        );

        $this->getServiceLocator()->get('Entity\Fee')->save($feeData);
    }

    /**
     * Get the latest fee type for a licence
     *
     * @todo Maybe move this so it can be re-used
     *
     * @param int $licenceId
     * @return int
     */
    protected function getFeeTypeForLicence($applicationId, $licenceId)
    {
        $data = $this->getServiceLocator()->get('Entity\Licence')->getTypeOfLicenceData($licenceId);

        $date = $this->getServiceLocator()->get('Entity\Application')->getApplicationDate($applicationId);

        return $this->getServiceLocator()->get('Data\FeeType')->getLatest(
            FeeTypeDataService::FEE_TYPE_GRANT,
            $data['goodsOrPsv'],
            $data['licenceType'],
            $date,
            ($data['niFlag'] === 'Y')
        );
    }

    protected function processApplicationOperatingCentres($id, $licenceId)
    {
        $applicationOperatingCentres = $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')
            ->getForApplication($id);

        $new = $updates = $deletions = array();

        foreach ($applicationOperatingCentres as $aoc) {
            switch ($aoc['action']) {
                case 'A':
                    $aoc['operatingCentre'] = $aoc['operatingCentre']['id'];
                    $aoc['licence'] = $licenceId;
                    $new[] = $aoc;
                    break;
            }
        }

        if (!empty($new)) {
            $licenceOperatingCentreService = $this->getServiceLocator()->get('Entity\LicenceOperatingCentre');
            foreach ($new as $aoc) {
                $licenceOperatingCentreService->save($aoc);
            }
        }

        // @todo Process updates and deletions (Out of scope for OLCS-4895)
    }

    protected function createDiscRecords($licenceId, $category)
    {
        $licenceVehicles = $this->getServiceLocator()->get('Entity\LicenceVehicle')
            ->getForApplicationValidation($licenceId);

        if (!empty($licenceVehicles)) {
            if ($category === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
                $this->createGoodsDiscs($licenceVehicles);
            } else {
                $this->createPsvDiscs($licenceId, count($licenceVehicles));
            }
        }
    }

    protected function createGoodsDiscs($licenceVehicles)
    {
        $defaults = array(
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N'
        );

        $goodsDiscService = $this->getServiceLocator()->get('Entity\GoodsDisc');

        foreach ($licenceVehicles as $licenceVehicle) {
            $data = array_merge(
                $defaults,
                array(
                    'licenceVehicle' => $licenceVehicle['id']
                )
            );
            $goodsDiscService->save($data);
        }
    }

    protected function createPsvDiscs($licenceId, $count)
    {
        $data = array(
            'licence' => $licenceId,
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N'
        );

        $this->getServiceLocator()->get('Entity\PsvDisc')->requestDiscs($count, $data);
    }

    protected function copyApplicationDataToLicence($id, $licenceId)
    {
        $licenceData = array_merge(
            array(
                'status' => LicenceEntityService::LICENCE_STATUS_VALID
            ),
            $this->getImportantLicenceDates(),
            $this->getApplicationDataForValidating($id)
        );

        $this->getServiceLocator()->get('Entity\Licence')->forceUpdate($licenceId, $licenceData);
    }

    protected function getApplicationDataForValidating($id)
    {
        if (!isset($this->applicationValidatingData[$id])) {
            $this->applicationValidatingData[$id] = $this->getServiceLocator()
                ->get('Entity\Application')->getDataForValidating($id);
        }

        return $this->applicationValidatingData[$id];
    }

    protected function getImportantLicenceDates()
    {
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();
        $reviewDate = date('Y-m-d', strtotime($date . ' +5 years'));
        $dom = date('j', strtotime($date));
        $expiryDate = date('Y-m-d', strtotime($date . ' +5 years -' . $dom . ' days'));

        return array(
            'inForceDate' => $date,
            'reviewDate' => $reviewDate,
            'expiryDate' => $expiryDate,
            'feeDate' => $expiryDate
        );
    }

    protected function setApplicationStatus($id, $status)
    {
        $data = array('status' => $status);

        $this->getServiceLocator()->get('Entity\Application')->forceUpdate($id, $data);
    }

    protected function getLicenceId($id)
    {
        return $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($id);
    }
}
