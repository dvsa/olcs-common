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
use Common\Controller\Lva\Adapters\VariationConditionsUndertakingsAdapter;
use Common\Service\Entity\CommunityLicEntityService;

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
        $licenceId = $this->getLicenceId($id);

        $this->setApplicationStatus($id, ApplicationEntityService::APPLICATION_STATUS_VALID);

        $this->copyApplicationDataToLicence($id, $licenceId);

        $this->processApplicationOperatingCentres($id, $licenceId);
        $this->processCommonGrantData($id, $licenceId);

        $category = $this->getServiceLocator()->get('Entity\Application')->getCategory($id);

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
        $licenceId = $this->getLicenceId($id);

        $this->grantApplication($id, ApplicationEntityService::APPLICATION_STATUS_VALID);

        $category = $this->getServiceLocator()->get('Entity\Application')->getCategory($id);

        // @NOTE This MUST happen before updating the licence record
        $this->createDiscRecords($licenceId, $category, $id);

        $licenceData = $this->getApplicationDataForValidating($id);
        $this->getServiceLocator()->get('Entity\Licence')->forceUpdate($licenceId, $licenceData);

        $this->processApplicationOperatingCentres($id, $licenceId);
        $this->processCommonGrantData($id, $licenceId);
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
    protected function getFeeTypeForApplication($applicationId, $feeType)
    {
        $applicationService = $this->getServiceLocator()->get('Entity\Application');

        $data = $applicationService->getTypeOfLicenceData($applicationId);
        $date = $applicationService->getApplicationDate($applicationId);

        return $this->getServiceLocator()->get('Data\FeeType')->getLatest(
            $feeType,
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
            $licenceVehicle['specifiedDate'] = $date;
        }

        $this->getServiceLocator()->get('Entity\LicenceVehicle')->multiUpdate($licenceVehicles);
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
        $this->grantConditionUndertakingData($id, $licenceId);

        $this->grantCommunityLicencesData($licenceId);

        $this->grantTransportManagerData($id, $licenceId);
    }

    protected function grantTransportManagerData($id, $licenceId)
    {
        $entityService = $this->getServiceLocator()->get('Entity\TransportManagerApplication');

        $results = $entityService->getGrantDataForApplication($id);

        foreach ($results as $row) {

            switch ($row['action']) {
                case 'A':
                    $this->createTransportManager($row, $licenceId);
                    break;
                case 'D':
                    $this->deleteTransportManager($row, $licenceId);
                    break;
            }
        }
    }

    protected function createTransportManager($data, $licenceId)
    {
        if (!$this->licenceHasTransportManager($data['transportManager']['id'], $licenceId)) {

            $data = $this->getServiceLocator()->get('Helper\Data')->replaceIds($data);

            unset($data['id']);
            unset($data['action']);
            unset($data['version']);
            unset($data['application']);

            // What do we do with this?
            unset($data['tmApplicationStatus']);

            $data['licence'] = $licenceId;

            $data['tmLicenceOcs'] = [];

            foreach ($data['tmApplicationOcs'] as $row) {
                $data['tmLicenceOcs'][] = $row['id'];
            }

            unset($data['tmApplicationOcs']);

            $entityService = $this->getServiceLocator()->get('Entity\TransportManagerLicence');
            $entityService->save($data);
        }
    }

    protected function licenceHasTransportManager($transportManagerId, $licenceId)
    {
        $results = $this->getServiceLocator()->get('Entity\TransportManagerLicence')
            ->getByTransportManagerAndLicence($transportManagerId, $licenceId);

        return !empty($results);
    }

    protected function deleteTransportManager($data, $licenceId)
    {

    }

    protected function grantCommunityLicencesData($licenceId)
    {
        $entityService = $this->getServiceLocator()->get('Entity\CommunityLic');

        $results = $entityService->getPendingForLicence($licenceId);

        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        foreach ($results as &$row) {
            $row['status'] = CommunityLicEntityService::STATUS_VALID;
            $row['specifiedDate'] = $date;
        }

        $entityService->multiUpdate($results);
    }

    protected function grantConditionUndertakingData($id, $licenceId)
    {
        $entityService = $this->getServiceLocator()->get('Entity\ConditionUndertaking');
        $results = $entityService->getGrantData($id);

        if (empty($results)) {
            return;
        }

        $user = $this->getServiceLocator()->get('Entity\User')->getCurrentUser();

        foreach ($results as $row) {
            switch ($row['action']) {
                case VariationConditionsUndertakingsAdapter::ACTION_ADDED:
                    $this->createConditionUndertaking($row, $licenceId, $user['id']);
                    break;
                case VariationConditionsUndertakingsAdapter::ACTION_UPDATED:
                    $this->updateConditionUndertaking($row, $user['id']);
                    break;
                case VariationConditionsUndertakingsAdapter::ACTION_DELETED:
                    $this->deleteConditionUndertaking($row, $user['id']);
                    break;
            }

            // Maybe we should clear up the delta's here?
        }
    }

    /**
     * Create a licence CU record from the ADD delta
     *
     * @param array $data
     * @param int $licenceId
     * @param int $approvedBy
     */
    protected function createConditionUndertaking($data, $licenceId, $approvedBy)
    {
        $data = $this->getServiceLocator()->get('Helper\Data')->replaceIds($data);

        unset($data['id']);
        unset($data['application']);
        unset($data['action']);
        unset($data['version']);

        $data['licence'] = $licenceId;
        $data['isDraft'] = 'N';
        $data['approvalUser'] = $approvedBy;

        $this->getServiceLocator()->get('Entity\ConditionUndertaking')->save($data);
    }

    /**
     * Update a licence CU record from the UPDATE delta
     *
     * @param array $data
     * @param int $approvedBy
     */
    protected function updateConditionUndertaking($data, $approvedBy)
    {
        $dataService = $this->getServiceLocator()->get('Helper\Data');
        $entityService = $this->getServiceLocator()->get('Entity\ConditionUndertaking');

        $licenceRecordId = $data['licConditionVariation']['id'];
        $licenceRecord = $dataService->replaceIds($entityService->getCondition($licenceRecordId));
        $deltaRecord = $dataService->replaceIds($data);

        $licenceRecord['approvalUser'] = $approvedBy;
        $licenceRecord['operatingCentre'] = $deltaRecord['operatingCentre'];
        $licenceRecord['conditionType'] = $deltaRecord['conditionType'];
        $licenceRecord['attachedTo'] = $deltaRecord['attachedTo'];
        $licenceRecord['isFulfilled'] = $deltaRecord['isFulfilled'];
        $licenceRecord['notes'] = $deltaRecord['notes'];

        // Not sure if these have any effect
        $licenceRecord['case'] = $deltaRecord['case'];
        $licenceRecord['addedVia'] = $deltaRecord['addedVia'];
        $licenceRecord['isDraft'] = 'N';
        unset($licenceRecord['action']);

        $entityService->forceUpdate($licenceRecordId, $licenceRecord);
    }

    /**
     * Delete condition undertaking during granting
     * @NOTE The AC specifies we need to set the approvalUser, so we need to make an UPDATE call before DELETE
     * which seems odd but as we are soft deleting, this gives us an audit trail
     *
     * @param array $data
     * @param int $approvedBy
     */
    protected function deleteConditionUndertaking($data, $approvedBy)
    {
        $entityService = $this->getServiceLocator()->get('Entity\ConditionUndertaking');

        $id = $data['licConditionVariation']['id'];

        $entityService->forceUpdate($id, ['approvalUser' => $approvedBy]);
        $entityService->delete($id);
    }
}
