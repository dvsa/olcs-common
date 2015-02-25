<?php

/**
 * Grant People Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Service\Lva\VariationPeopleLvaService;

/**
 * Grant People Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class GrantPeopleProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function grant($applicationId, $licenceId)
    {
        $entityService = $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson');

        $results = $entityService->getAllByApplication($applicationId);

        if (empty($results)) {
            return;
        }

        $user = $this->getServiceLocator()->get('Entity\User')->getCurrentUser();

        foreach ($results as $row) {
            switch ($row['action']) {
                case VariationPeopleLvaService::ACTION_ADDED:
                    $this->createOrganisationPerson($row);
                    break;
                case VariationPeopleLvaService::ACTION_UPDATED:
                    $this->createOrganisationPerson($row);
                    break;
                case VariationPeopleLvaService::ACTION_DELETED:
                    $this->createOrganisationPerson($row);
                    break;
            }
        }
    }
}
