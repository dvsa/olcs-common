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

    public function grant($applicationId)
    {
        $entityService = $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson');

        $results = $entityService->getAllByApplication($applicationId);

        if (empty($results)) {
            return;
        }

        $user = $this->getServiceLocator()->get('Entity\User')->getCurrentUser();

        foreach ($results['Results'] as $row) {
            switch ($row['action']) {
                case VariationPeopleLvaService::ACTION_ADDED:
                    $this->createOrganisationPerson($row);
                    break;
                case VariationPeopleLvaService::ACTION_UPDATED:
                    $this->updateOrganisationPerson($row);
                    break;
                case VariationPeopleLvaService::ACTION_DELETED:
                    $this->deleteOrganisationPerson($row);
                    break;
            }
        }
    }

    private function createOrganisationPerson($data)
    {
        $dataService = $this->getServiceLocator()->get('Helper\Data');

        $personData = $this->cleanData($data['person']);

        $person = $this->getServiceLocator()->get('Entity\Person')->save($personData);

        $orgData = $dataService->replaceIds(
            $this->cleanData($data, ['action', 'originalPerson', 'person'])
        );
        $orgData['person'] = $person['id'];
        // @TODO addedDate

        $this->getServiceLocator()->get('Entity\OrganisationPerson')->save($orgData);
    }

    private function updateOrganisationPerson($data)
    {
        $orgId    = $data['organisation']['id'];
        $personId = $data['originalPerson']['id'];

        $this->deleteByOrgAndPersonId($orgId, $personId);
        $this->createOrganisationPerson($data);
    }

    private function deleteOrganisationPerson($data)
    {
        $orgId    = $data['organisation']['id'];
        $personId = $data['person']['id'];
        $this->deleteByOrgAndPersonId($orgId, $personId);
    }

    private function cleanData($input, $extraKeys = [])
    {
        $keys = array_merge(
            ['id', 'version', 'createdOn', 'lastModifiedOn'],
            $extraKeys
        );
        foreach ($keys as $key) {
            unset($input[$key]);
        }

        return $input;
    }

    private function deleteByOrgAndPersonId($orgId, $personId)
    {
        $this->getServiceLocator()
            ->get('Entity\OrganisationPerson')
            ->deleteByOrgAndPersonId($orgId, $personId);

        $this->getServiceLocator()
            ->get('Entity\Person')
            ->delete($personId);
    }
}
