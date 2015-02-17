<?php

/**
 * Community Lic Suspension Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Community Lic Suspension Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicSuspensionEntityService extends AbstractEntityService
{
    protected $entity = 'CommunityLicSuspension';

    /**
     * Delete CommunityLicSuspension and CommunityLicSuspension Reasons by CommunityLic ids
     *
     * @param array $ids
     */
    public function deleteSuspensionsAndReasons($ids)
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
                    'communityLicSuspension' => 'IN [' . implode(',', $reasonIds) . ']'
                ];
                $this->getServiceLocator()->get('Entity\CommunityLicSuspensionReason')->deleteList($reasonsQuery);
            }
            $this->deleteList($query);
        }
    }
}
