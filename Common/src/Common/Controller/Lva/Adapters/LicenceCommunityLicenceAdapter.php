<?php

/**
 * Licence Community Licence Adapter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractControllerAwareAdapter;
use Common\Controller\Lva\Interfaces\CommunityLicenceAdapterInterface;
use Common\Service\Entity\CommunityLicEntityService;

/**
 * Licence Community Licence Adapter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceCommunityLicenceAdapter extends AbstractControllerAwareAdapter implements
    CommunityLicenceAdapterInterface
{
    protected $lva = 'licence';

    /**
     * Add office copy
     *
     * @param int $licenceId
     */
    public function addOfficeCopy($licenceId)
    {
        $data = [
            'specifiedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(),
            'status' => CommunityLicEntityService::STATUS_ACTIVE
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
        $licence = $this->getServiceLocator()->get('Entity\Licence')->getById($id);
        return isset($licence['totAuthVehicles']) ? $licence['totAuthVehicles'] : 0;
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
            'specifiedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(),
            'status' => CommunityLicEntityService::STATUS_ACTIVE,
        ];

        $identifiers = $this->getServiceLocator()->get('Entity\CommunityLic')->addCommunityLicences($data, $licenceId, $totalLicences);

        return $this->getServiceLocator()
            ->get('Helper\CommunityLicenceDocument')
            ->generateBatch($identifiers['id']);
    }
}
