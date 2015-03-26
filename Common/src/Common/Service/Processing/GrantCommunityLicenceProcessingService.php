<?php

/**
 * Grant Community Licences Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Service\Entity\CommunityLicEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Grant Community Licences Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantCommunityLicenceProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function grant($licenceId)
    {
        $entityService = $this->getServiceLocator()->get('Entity\CommunityLic');

        $results = $entityService->getPendingForLicence($licenceId);

        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $identifiers = [];
        foreach ($results as &$row) {
            $identifiers[] = $row['id'];
            $row['status'] = CommunityLicEntityService::STATUS_ACTIVE;
            $row['specifiedDate'] = $date;
        }

        $entityService->multiUpdate($results);

        $this->getServiceLocator()
            ->get('Helper\CommunityLicenceDocument')
            ->generateBatch($licenceId, $identifiers);
    }

    /**
     * Grant or Void active/pending "community licences" for a licence dependant on the type of licence
     * 
     * @param int $licenceId A licence ID
     * 
     * @return void
     */
    public function voidOrGrant($licenceId)
    {
        $licence = $this->getServiceLocator()->get('Entity\Licence')->getOverview($licenceId);

        if ($this->canHaveCommunityLicences($licence)) {
            $this->grant($licenceId);
        } else {
            $this->voidActivePending($licenceId);
        }
    }

    /**
     * Can a licence have community licences
     * 
     * @param array $licence array of Licence data from LicenceEnityService
     * 
     * @return bool
     */
    public function canHaveCommunityLicences($licence)
    {
        // Any standard national is allowed
        if ($licence['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
            return true;
        }

        // Restricted PSV is allowed
        if ($licence['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED &&
            $licence['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_PSV) {
            return true;
        }

        // everything else not allowed
        return false;
    }

    /**
     * Void all Active and Pending Community licences
     * 
     * @param int $licenceId Licence ID
     * 
     * @return void
     */
    public function voidActivePending($licenceId)
    {
        /* @var $entityService \Common\Service\Entity\CommunityLicEntityService */
        $entityService = $this->getServiceLocator()->get('Entity\CommunityLic');

        $date = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $activePendingLicences = $entityService->getActivePendingLicences($licenceId);
        foreach ($activePendingLicences as &$row) {
            $row['status'] = CommunityLicEntityService::STATUS_RETURNDED;
            $row['expiredDate'] = $date;
        }

        $entityService->multiUpdate($activePendingLicences);
    }
}
