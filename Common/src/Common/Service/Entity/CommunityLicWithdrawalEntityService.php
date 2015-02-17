<?php

/**
 * Community Lic Withdrawal Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Community Lic Withdrawal Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicWithdrawalEntityService extends AbstractEntityService
{
    protected $entity = 'CommunityLicWithdrawal';

    /**
     * Delete CommunityLicWithdrawal and CommunityLicWithdrawalReasons by CommunityLic ids
     *
     * @param array $ids
     */
    public function deleteWithdrawalsAndReasons($ids)
    {
        if (count($ids)) {
            $reasonIds = [];
            $query = [
                'communityLic' => 'IN [' . implode(',', $ids) . ']'
            ];
            $reasons = $this->get($query);
            if ($reasons['Count']) {
                foreach ($reasons['Results'] as $reason) {
                    $reasonIds[] = $reason['id'];
                }
                $reasonsQuery = [
                    'communityLicWithdrawal' => 'IN [' . implode(',', $reasonIds) . ']'
                ];
                $this->getServiceLocator()->get('Entity\CommunityLicWithdrawalReason')->deleteList($reasonsQuery);
            }
            $this->deleteList($query);
        }
    }
}
