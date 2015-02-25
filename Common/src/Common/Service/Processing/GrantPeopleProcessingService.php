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

    /**
     * Update all organisation people based on relevant deltas
     * in application_organisation_person
     *
     * @param int $applicationId
     */
    public function grant($applicationId)
    {
        $results = $this->getServiceLocator()
            ->get('Entity\ApplicationOrganisationPerson')
            ->getAllByApplication($applicationId);

        if ($results['Count'] === 0) {
            return;
        }

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

    /**
     * Create a person and associate it with a new organisation
     * person record
     *
     * @param array $data
     */
    private function createOrganisationPerson($data)
    {
        $person = $this->getServiceLocator()
            ->get('Entity\Person')
            ->save(
                $this->cleanData($data['person'])
            );

        $orgData = $this->getServiceLocator()
            ->get('Helper\Data')
            ->replaceIds(
                $this->cleanData($data, ['action', 'originalPerson', 'person'])
            );

        $orgData['person'] = $person['id'];
        $orgData['addedDate'] = $this->getServiceLocator()->get('Helper\Date')->getDate();

        $this->getServiceLocator()->get('Entity\OrganisationPerson')->save($orgData);
    }

    /**
     * Updates are actually just a combination of a delete
     * of the original person and an add of the new one
     *
     * @param array $data
     */
    private function updateOrganisationPerson($data)
    {
        $orgId    = $data['organisation']['id'];
        $personId = $data['originalPerson']['id'];

        $this->deleteByOrgAndPersonId($orgId, $personId);
        $this->createOrganisationPerson($data);
    }

    /**
     * Delete a person
     *
     * @param array $data
     */
    private function deleteOrganisationPerson($data)
    {
        $orgId    = $data['organisation']['id'];
        $personId = $data['person']['id'];

        $this->deleteByOrgAndPersonId($orgId, $personId);
    }

    /**
     * Helper to delete both an org row and the person it
     * links to
     *
     * @param int $orgId
     * @param int $personId
     */
    private function deleteByOrgAndPersonId($orgId, $personId)
    {
        $this->getServiceLocator()
            ->get('Entity\OrganisationPerson')
            ->deleteByOrgAndPersonId($orgId, $personId);

        // @TODO confirm: AC says to delete the person, but it
        // might exist in other organisations. If we need to check
        // if it's the last one before delete then push this behind
        // the OrgPerson entity and DRY up in the people adapter
        // or controller (can't remember where it does this at the mo)
        /*
        $this->getServiceLocator()
            ->get('Entity\Person')
            ->delete($personId);
         */
    }

    /**
     * Helper method to clean typical keys out of their source
     * entities so they don't accidentally get applied to
     * their destination rows
     *
     * @param array $input
     * @param array $extraKeys
     */
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
}
