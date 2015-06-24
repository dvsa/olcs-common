<?php

/**
 * @todo to be removed after community licence section will be completely done
 * Licence Community Licence Adapter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractControllerAwareAdapter;
use Common\Service\Entity\CommunityLicEntityService;

/**
 * Licence Community Licence Adapter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceCommunityLicenceAdapter extends AbstractControllerAwareAdapter
{
    protected $lva = 'licence';

    /**
     * Add community licences with specific issue numbers
     *
     * @param int $licenceId
     * @param int $totalLicences
     */
    public function addCommunityLicencesWithIssueNos($licenceId, $issueNos)
    {
        $data = [
            'specifiedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(),
            'status' => CommunityLicEntityService::STATUS_ACTIVE,
        ];

        $identifiers = $this->getServiceLocator()->get('Entity\CommunityLic')
            ->addCommunityLicencesWithIssueNos($data, $licenceId, $issueNos);

        // send to print scheduler
        return $this->getServiceLocator()->get('Helper\CommunityLicenceDocument')
            ->generateBatch($licenceId, $identifiers['id']);
    }
}
