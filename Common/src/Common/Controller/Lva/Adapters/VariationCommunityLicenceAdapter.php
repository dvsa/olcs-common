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
        $interimData = $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForInterim($identifier);
        $interimStatus = isset($interimData['interimStatus']['id']) ? $interimData['interimStatus']['id'] : '';
        if ($interimStatus !== ApplicationEntityService::INTERIM_STATUS_INFORCE) {
            $data = [
                'status' => CommunityLicEntityService::STATUS_PENDING,
            ];
        } else {
            $data = [
                'status' => CommunityLicEntityService::STATUS_ACTIVE,
                'specifiedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate()
            ];
        }
        $id = $this->getServiceLocator()->get('Entity\CommunityLic')->addOfficeCopy($data, $licenceId);
        if ($interimStatus == ApplicationEntityService::INTERIM_STATUS_INFORCE) {
            $this->getServiceLocator()
                ->get('Helper\CommunityLicenceDocument')
                ->generateBatch($licenceId, [$id['id']], $identifier);
        }
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
        $interimData = $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForInterim($identifier);

        $interimStatus = isset($interimData['interimStatus']['id']) ? $interimData['interimStatus']['id'] : '';
        if ($interimStatus !== ApplicationEntityService::INTERIM_STATUS_INFORCE) {
            $data = [
                'status' => CommunityLicEntityService::STATUS_PENDING,
            ];
        } else {
            $data = [
                'status' => CommunityLicEntityService::STATUS_ACTIVE,
                'specifiedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate()
            ];
        }

        $identifiers = $this->getServiceLocator()
            ->get('Entity\CommunityLic')
            ->addCommunityLicences($data, $licenceId, $totalLicences);

        if ($interimStatus == ApplicationEntityService::INTERIM_STATUS_INFORCE) {
            $this->getServiceLocator()
                ->get('Helper\CommunityLicenceDocument')
                ->generateBatch($licenceId, $identifiers['id'], $identifier);
        }
    }
}
