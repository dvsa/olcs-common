<?php

/**
 * Application Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Service\Data\FeeTypeDataService;
use Common\Service\Data\CategoryDataService;
use Common\Service\Entity\FeeEntityService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\TrafficAreaEntityService;
use Common\Service\Processing\ApplicationSnapshotProcessingService;
use Common\Service\Entity\ApplicationTrackingEntityService as Tracking;
use Common\Service\Entity\ApplicationCompletionEntityService as Completion;

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

    /**
     * Called when an application is validated (When GV fee is paid, or when PSV is granted)
     *
     * @param int $id
     */
    public function validateApplication($id)
    {
        $this->processPreGrantData($id);

        $licenceId = $this->getLicenceId($id);

        $this->setApplicationStatus($id, ApplicationEntityService::APPLICATION_STATUS_VALID);

        $this->copyApplicationDataToLicence($id, $licenceId);

        $this->processApplicationOperatingCentres($id, $licenceId);
        $this->processCommonGrantData($id, $licenceId);

        $category = $this->getServiceLocator()->get('Entity\Application')->getCategory($id);

        if ($category === LicenceEntityService::LICENCE_TYPE_GOODS_VEHICLE) {
            $this->getServiceLocator()->get('Entity\Fee')->cancelInterimForApplication($id);
        }

        $this->createDiscRecords($licenceId, $category, $id);

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addSuccessMessage('licence-valid-confirmation');
    }

    /**
     * Called when granting an NEW application
     *
     * @param int $id
     */
    public function processGrantApplication($id)
    {
        $licenceId = $this->getLicenceId($id);

        $category = $this->getServiceLocator()->get('Entity\Application')->getCategory($id);

        if ($category === LicenceEntityService::LICENCE_CATEGORY_PSV) {
            $this->processGrantPsvApplication($id, $licenceId);
        } else {
            $this->processGrantGoodsApplication($id, $licenceId);
        }
    }

    /**
     * Called when granting a variation application
     *
     * @param int $id
     */
    public function processGrantVariation($id)
    {
        $this->processPreGrantData($id);

        $licenceId = $this->getLicenceId($id);

        $this->grantApplication($id, ApplicationEntityService::APPLICATION_STATUS_VALID);

        $category = $this->getServiceLocator()->get('Entity\Application')->getCategory($id);

        $applicationType = $this->getServiceLocator()->get('Entity\Application')->getLicenceType($id);
        $licenceType = $this->getServiceLocator()->get('Entity\Licence')->getOverview($licenceId);

        if ($applicationType['licenceType']['id'] !== $licenceType['licenceType']['id']) {
            $this->updateExistingDiscs($licenceId, $id, $category);
        }

        // @NOTE This MUST happen before updating the licence record
        $this->createDiscRecords($licenceId, $category, $id);

        $licenceData = $this->getApplicationDataForValidating($id);
        $this->getServiceLocator()->get('Entity\Licence')->forceUpdate($licenceId, $licenceData);

        $this->processApplicationOperatingCentres($id, $licenceId);
        $this->processCommonGrantData($id, $licenceId);
    }

    /**
     * If the application type has changed we need to void all the existing
     * discs on the licence and create a load of new ones with the updated
     * type
     */
    protected function updateExistingDiscs($licenceId, $applicationId, $category)
    {
        if ($category === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $this->getServiceLocator()
                ->get('Entity\GoodsDisc')
                ->updateExistingForLicence($licenceId, $applicationId);
        } else {
            $this->getServiceLocator()
                ->get('Entity\PsvDisc')
                ->updateExistingForLicence($licenceId);
        }
    }

    /**
     * Called when un-granting an application
     *
     * @param int $id
     */
    public function processUnGrantApplication($id)
    {
        $licenceId = $this->getLicenceId($id);

        $this->undoGrantApplication($id);
        $this->undoGrantLicence($licenceId);

        $this->cancelFees($licenceId);
        $this->closeGrantTask($id, $licenceId);
    }

    public function cancelFees($licenceId)
    {
        $this->getServiceLocator()->get('Entity\Fee')->cancelForLicence($licenceId);
    }

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
            'feeStatus' => FeeEntityService::STATUS_OUTSTANDING,
            'task' => $taskId
        );

        $this->getServiceLocator()->get('Entity\Fee')->save($feeData);
    }

    /**
     * @param int $applicationId
     * @param int $licenceId pass this in to save making an extra REST call
     *
     * @return boolean true if a fee was created, false otherwise (fee already exists)
     */
    public function maybeCreateVariationFee($applicationId, $licenceId)
    {
        $fee = $this->getServiceLocator()->get('Entity\Fee')
            ->getLatestOutstandingFeeForApplication($applicationId);

        if (!empty($fee)) {
            // existing fee, don't create one
            return false;
        }

        $this->createFee($applicationId, $licenceId, FeeTypeDataService::FEE_TYPE_VAR);

        return true;
    }

    /**
     * @param int $applicationId
     *
     * @return boolean true if a fee was cancelled, false otherwise (no fee exists)
     */
    public function maybeCancelVariationFee($applicationId)
    {
        $fee = $this->getServiceLocator()->get('Entity\Fee')
            ->getLatestOutstandingFeeForApplication($applicationId);

        if (!empty($fee)) {
            // existing fee, cancel it
            $this->getServiceLocator()->get('Entity\Fee')
                ->cancelForApplication($applicationId);
            return true;
        }

        return false;
    }

    /**
     * @param int $applicationId
     * @param array $requiredSections
     */
    public function trackingIsValid($applicationId, $requiredSections)
    {
        $statuses = $this->getServiceLocator()->get('Entity\ApplicationTracking')
            ->getTrackingStatuses($applicationId);

        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        $validStatuses = [Tracking::STATUS_ACCEPTED, Tracking::STATUS_NOT_APPLICABLE];

        foreach ($requiredSections as $section) {
            $key = lcfirst($stringHelper->underscoreToCamel($section).'Status');
            if (!in_array($statuses[$key], $validStatuses)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $applicationId
     * @param array $requiredSections
     */
    public function sectionCompletionIsValid($applicationId, $requiredSections)
    {
        $completions = $this->getServiceLocator()->get('Entity\ApplicationCompletion')
            ->getCompletionStatuses($applicationId);

        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        foreach ($requiredSections as $section) {
            $key = lcfirst($stringHelper->underscoreToCamel($section).'Status');
            if (!isset($completions[$key]) || $completions[$key] !== Completion::STATUS_COMPLETE) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $applicationId
     * @param array $requiredSections
     */
    public function getIncompleteSections($applicationId, $requiredSections)
    {
        $completions = $this->getServiceLocator()->get('Entity\ApplicationCompletion')
            ->getCompletionStatuses($applicationId);

        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        $incomplete = [];
        foreach ($requiredSections as $section) {
            $key = lcfirst($stringHelper->underscoreToCamel($section).'Status');
            if ($completions[$key] !== Completion::STATUS_COMPLETE) {
                $incomplete[] = $section;
            }
        }

        return $incomplete;
    }

    /**
     * @param int $applicationId
     * @param boolean if there are any outstanding fees
     */
    public function feeStatusIsValid($applicationId)
    {
        $fees = $this->getServiceLocator()->get('Entity\Fee')->getOutstandingFeesForApplication($applicationId);
        return empty($fees);
    }

    public function getInterimFee($applicationId)
    {
        return $this->getFeeForApplicationByType($applicationId, FeeTypeDataService::FEE_TYPE_GRANTINT);
    }

    public function getApplicationFee($applicationId)
    {
        $applicationType = $this->getServiceLocator()->get('Entity\Application')
            ->getApplicationType($applicationId);

        $feeType =($applicationType == ApplicationEntityService::APPLICATION_TYPE_VARIATION)
            ? FeeTypeDataService::FEE_TYPE_VAR
            : FeeTypeDataService::FEE_TYPE_APP;

        return $this->getFeeForApplicationByType($applicationId, $feeType);
    }

    protected function getFeeForApplicationByType($applicationId, $feeType)
    {
        $feeTypeData = $this->getFeeTypeForApplication(
            $applicationId,
            $feeType
        );

        return $this->getServiceLocator()->get('Entity\Fee')->getLatestFeeByTypeStatusesAndApplicationId(
            $feeTypeData['id'],
            [FeeEntityService::STATUS_OUTSTANDING, FeeEntityService::STATUS_WAIVE_RECOMMENDED],
            $applicationId
        );
    }

    protected function createGrantFee($applicationId, $licenceId, $taskId)
    {
        $this->createFee($applicationId, $licenceId, FeeTypeDataService::FEE_TYPE_GRANT, $taskId);
    }

    protected function closeGrantTask($id, $licenceId)
    {
        $this->getServiceLocator()->get('Entity\Task')->closeByQuery(
            array(
                'category' => CategoryDataService::CATEGORY_APPLICATION,
                'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE,
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
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE,
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

    /**
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
            ($data['niFlag'] === 'Y') ? TrafficAreaEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE : null
        );
    }

    protected function processApplicationOperatingCentres($id, $licenceId)
    {
        $applicationOperatingCentres = $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')
            ->getForApplication($id);

        $new = $updates = $deletions = $clearInterims = array();

        foreach ($applicationOperatingCentres as $aoc) {

            if ($aoc['isInterim']) {
                $clearInterims[] = $aoc['id'];
            }

            switch ($aoc['action']) {
                case 'A':
                case 'U':
                    $action = $aoc['action'];

                    unset($aoc['id']);
                    unset($aoc['action']);
                    unset($aoc['version']);
                    unset($aoc['createdOn']);
                    unset($aoc['createdBy']);
                    unset($aoc['modifiedOn']);
                    unset($aoc['modifiedBy']);

                    $aoc['operatingCentre'] = $aoc['operatingCentre']['id'];
                    $aoc['licence'] = $licenceId;

                    if ($action === 'A') {
                        $new[] = $aoc;
                    } else {
                        $updates[] = $aoc;
                    }
                    break;
                case 'D':
                    $deletions[] = $aoc['operatingCentre']['id'];
                    break;
            }
        }

        if (!empty($new) || !empty($updates) || !empty($deletions)) {
            $licenceOperatingCentreService = $this->getServiceLocator()->get('Entity\LicenceOperatingCentre');

            if (!empty($new)) {
                foreach ($new as $aoc) {
                    $licenceOperatingCentreService->save($aoc);
                }
            }

            if (!empty($deletions)) {
                foreach ($deletions as $ocId) {
                    $licenceOperatingCentreService->deleteList(['operatingCentre' => $ocId]);
                }
            }

            if (!empty($updates)) {

                $locs = $licenceOperatingCentreService->getListForLva($licenceId);

                foreach ($updates as &$aoc) {
                    foreach ($locs['Results'] as $loc) {
                        if ($loc['operatingCentre']['id'] !== $aoc['operatingCentre']) {
                            continue;
                        }

                        $licenceOperatingCentreService->forceUpdate($loc['id'], $aoc);
                    }
                }
            }
        }

        if (!empty($clearInterims)) {
            foreach ($clearInterims as $aocId) {
                $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')->forceUpdate($aocId, ['isInterim' => null]);
            }
        }
    }

    protected function getDifferenceInTotalAuth($licenceId, $applicationId)
    {
        $licence = $this->getServiceLocator()->get('Entity\Licence')->getById($licenceId);
        $application = $this->getServiceLocator()->get('Entity\Application')->getById($applicationId);

        $totalApplicationAuth = (
            (int)$application['totAuthLargeVehicles'] +
            (int)$application['totAuthMediumVehicles'] +
            (int)$application['totAuthSmallVehicles']
        );

        $totalLicenceAuth = (
            (int)$licence['totAuthLargeVehicles'] +
            (int)$licence['totAuthMediumVehicles'] +
            (int)$licence['totAuthSmallVehicles']
        );

        return $totalApplicationAuth - $totalLicenceAuth;
    }

    protected function createDiscRecords($licenceId, $category, $applicationId)
    {
        if ($category === LicenceEntityService::LICENCE_CATEGORY_PSV) {

            $difference = $this->getDifferenceInTotalAuth($licenceId, $applicationId);

            if ($difference > 0) {
                $this->createPsvDiscs($licenceId, $difference);
            }
        }

        $licenceVehicles = $this->getServiceLocator()->get('Entity\LicenceVehicle')
            ->getForApplicationValidation($licenceId, $applicationId);

        if (!empty($licenceVehicles)) {
            if ($category === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
                $this->createGoodsDiscs($licenceVehicles);
            }

            $this->specifyVehicles($licenceVehicles);
        }
    }

    protected function specifyVehicles($licenceVehicles)
    {
        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        // @NOTE passing licenceVehicle by reference
        foreach ($licenceVehicles as &$licenceVehicle) {
            // some vehicles might have a specified date if they were
            // interims
            if ($licenceVehicle['specifiedDate'] === null) {
                $licenceVehicle['specifiedDate'] = $date;
            }
            $licenceVehicle['interimApplication'] = null;
        }

        $this->getServiceLocator()->get('Entity\LicenceVehicle')->multiUpdate($licenceVehicles);
    }

    protected function createGoodsDiscs($licenceVehicles)
    {
        $this->getServiceLocator()->get('Entity\GoodsDisc')
            ->createForVehicles($licenceVehicles);
    }

    protected function createPsvDiscs($licenceId, $count)
    {
        $this->getServiceLocator()->get('Entity\PsvDisc')
            ->requestBlankDiscs($licenceId, $count);
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

    protected function processGrantPsvApplication($id, $licenceId)
    {
        $this->processPreGrantData($id);

        $appStatus = ApplicationEntityService::APPLICATION_STATUS_VALID;
        $licStatus = LicenceEntityService::LICENCE_STATUS_VALID;

        $this->grantApplication($id, $appStatus);
        $this->grantLicence($licenceId, $licStatus);

        $this->copyApplicationDataToLicence($id, $licenceId);

        $dataForValidating = $this->getApplicationDataForValidating($id);

        if ($dataForValidating['licenceType'] !== LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            $this->processApplicationOperatingCentres($id, $licenceId);
        }

        $this->processCommonGrantData($id, $licenceId);

        $this->createDiscRecords($licenceId, LicenceEntityService::LICENCE_CATEGORY_PSV, $id);
    }

    protected function processGrantGoodsApplication($id, $licenceId)
    {
        $this->grantApplication($id);
        $this->grantLicence($licenceId);

        $taskId = $this->createGrantTask($id, $licenceId);
        $this->createGrantFee($id, $licenceId, $taskId);
    }

    /**
     * Common logic to grant data
     * - called when validating a NEW GV App
     * - called when granting a NEW PSV App
     * - called when granting a variation
     *
     * @param int $id
     * @param int $licenceId
     */
    protected function processCommonGrantData($id, $licenceId)
    {
        $this->getServiceLocator()->get('Processing\GrantConditionUndertaking')->grant($id, $licenceId);

        $this->getServiceLocator()->get('Processing\GrantCommunityLicence')->grant($licenceId);

        $this->getServiceLocator()->get('Processing\GrantTransportManager')->grant($id, $licenceId);

        $this->getServiceLocator()->get('Processing\GrantPeople')->grant($id);

        $this->getServiceLocator()->get('Processing\Licence')->generateDocument($licenceId);
    }

    protected function processPreGrantData($id)
    {
        $this->getServiceLocator()->get('Processing\ApplicationSnapshot')
            ->storeSnapshot($id, ApplicationSnapshotProcessingService::ON_GRANT);
    }

    /**
     * Called when withdrawing an application
     *
     * @param int $id
     * @param string $reason
     *  ApplicationEntityService::WITHDRAWN_REASON_WITHDRAWN|ApplicationEntityService::WITHDRAWN_REASON_REG_IN_ERROR
     */
    public function processWithdrawApplication($id, $reason)
    {
        $applicationEntityService = $this->getServiceLocator()->get('Entity\Application');

        // Set the application status to 'Withdrawn'
        // Set the withdrawn date on the application to the current date
        // Record the withdrawal reason
        $data = [
            'status' => ApplicationEntityService::APPLICATION_STATUS_WITHDRAWN,
            'withdrawnDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(),
            'withdrawnReason' => $reason,
        ];
        $applicationEntityService->forceUpdate($id, $data);

        // If it is a new application (as opposed to a variation), update the licence status to 'Withdrawn'
        $applicationType = $applicationEntityService->getApplicationType($id);
        if ($applicationType == ApplicationEntityService::APPLICATION_TYPE_NEW) {
            $this->getServiceLocator()->get('Entity\Licence')->setLicenceStatus(
                $this->getLicenceId($id),
                LicenceEntityService::LICENCE_STATUS_WITHDRAWN
            );
        }

        // Void any interim discs associated to vehicles linked to the current application
        $this->getServiceLocator()->get('Helper\Interim')->voidDiscsForApplication($id);
    }

    /**
     * Called when refusing an application
     *
     * @param int $id
     */
    public function processRefuseApplication($id)
    {
        $applicationEntityService = $this->getServiceLocator()->get('Entity\Application');

        // Set the application status to 'Refused'
        // Set the refused date on the application to the current date
        // Record the withdrawal reason
        $data = [
            'status' => ApplicationEntityService::APPLICATION_STATUS_REFUSED,
            'refusedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(),
        ];
        $applicationEntityService->forceUpdate($id, $data);

        // If it is a new application (as opposed to a variation), update the licence status to 'Refused'
        $applicationType = $applicationEntityService->getApplicationType($id);
        if ($applicationType == ApplicationEntityService::APPLICATION_TYPE_NEW) {
            $this->getServiceLocator()->get('Entity\Licence')->setLicenceStatus(
                $this->getLicenceId($id),
                LicenceEntityService::LICENCE_STATUS_REFUSED
            );
        }

        // Void any interim discs associated to vehicles linked to the current application
        $this->getServiceLocator()->get('Helper\Interim')->voidDiscsForApplication($id);
    }
}
