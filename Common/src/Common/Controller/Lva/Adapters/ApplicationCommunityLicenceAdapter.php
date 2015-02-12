<?php

/**
 * Application Community Licence Adapter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractControllerAwareAdapter;
use Common\Controller\Lva\Interfaces\CommunityLicenceAdapterInterface;
use Common\Service\Entity\CommunityLicEntityService;

/**
 * Application Community Licence Adapter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ApplicationCommunityLicenceAdapter extends AbstractControllerAwareAdapter implements
    CommunityLicenceAdapterInterface
{
    protected $lva = 'application';

    /**
     * Create office copy
     *
     * @param int $licenceId
     */
    public function addOfficeCopy($licenceId)
    {
        $data = [
            'status' => CommunityLicEntityService::STATUS_PENDING
        ];
        $this->getServiceLocator()->get('Entity\CommunityLic')->addOfficeCopy($data, $licenceId);
    }

    /**
     * Get total authority
     *
     * @param int $id
     */
    public function getTotalAuthority($id)
    {
        $application = $this->getServiceLocator()->get('Entity\Application')->getById($id);
        return isset($application['totAuthVehicles']) ? $application['totAuthVehicles'] : 0;
    }

    /**
     * Add community licences
     *
     * @param int $licenceId
     * @param int $totalLicences
     */
    public function addCommunityLicences($licenceId, $totalLicences)
    {
        $data = [
            'status' => CommunityLicEntityService::STATUS_PENDING,
        ];
        $this->getServiceLocator()->get('Entity\CommunityLic')->addCommunityLicences($data, $licenceId, $totalLicences);
    }
}
