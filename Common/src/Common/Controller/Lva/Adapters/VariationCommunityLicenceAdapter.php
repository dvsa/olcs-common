<?php

/**
 * Variation Community Licence Adapter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractControllerAwareAdapter;
use Common\Controller\Lva\Interfaces\CommunityLicenceAdapterInterface;
use Common\Service\Entity\CommunityLicEntityService;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Variation Community Licence Adapter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VariationCommunityLicenceAdapter extends AbstractControllerAwareAdapter implements
    CommunityLicenceAdapterInterface
{
    protected $lva = 'variation';

    /**
     * Create office copy
     *
     * @param int $licenceId
     * @param int $identifier
     */
    public function addOfficeCopy($licenceId, $identifier)
    {
        return $this->getServiceLocator()
            ->get('ApplicationCommunityLicenceAdapter')
            ->addOfficeCopy($licenceId, $identifier);
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
     * @param int $identifier
     */
    public function addCommunityLicences($licenceId, $totalLicences, $identifier)
    {
        return $this->getServiceLocator()
            ->get('ApplicationCommunityLicenceAdapter')
            ->addCommunityLicences($licenceId, $totalLicences, $identifier);
    }
}
