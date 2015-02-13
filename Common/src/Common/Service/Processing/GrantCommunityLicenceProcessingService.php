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

        foreach ($results as &$row) {
            $row['status'] = CommunityLicEntityService::STATUS_VALID;
            $row['specifiedDate'] = $date;
        }

        $entityService->multiUpdate($results);
    }
}
