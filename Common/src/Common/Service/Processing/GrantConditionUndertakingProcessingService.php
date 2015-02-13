<?php

/**
 * Grant Condition Undertaking Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Controller\Lva\Adapters\VariationConditionsUndertakingsAdapter;

/**
 * Grant Condition Undertaking Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantConditionUndertakingProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function grant($id, $licenceId)
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
