<?php

/**
 * Variation People LVA service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Lva;

use Common\Service\Entity\OrganisationEntityService;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Variation People LVA service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VariationPeopleLvaService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const ACTION_ADDED = 'A';
    const ACTION_EXISTING = 'E';
    const ACTION_CURRENT = 'C';
    const ACTION_UPDATED = 'U';
    const ACTION_DELETED = 'D';

    const SOURCE_APPLICATION = 'A';
    const SOURCE_ORGANISATION = 'O';

    private $tableData = [];

    public function getTableData($orgId, $appId)
    {
        if (empty($this->tableData)) {
            $orgPeople = $this->getServiceLocator()->get('Entity\Person')
                ->getAllForOrganisation($orgId)['Results'];

            $applicationPeople = $this->getServiceLocator()->get('Entity\Person')
                ->getAllForApplication($appId)['Results'];

            $this->tableData = $this->updateAndFilterTableData(
                $this->indexRows(self::SOURCE_ORGANISATION, $orgPeople),
                $this->indexRows(self::SOURCE_APPLICATION, $applicationPeople)
            );
        }

        return $this->tableData;
    }

    public function deletePerson($orgId, $id, $appId)
    {
        $appPerson = $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
            ->getByApplicationAndPersonId($appId, $id);

        // an application person is a straight forward delete
        if ($appPerson) {
            return $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
                ->deletePerson($appPerson['id'], $id);
        }

        // must be an org one then; create a delta record
        $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
            ->variationDelete($id, $orgId, $appId);
    }

    public function restorePerson($orgId, $id, $appId)
    {
        // @TODO methodize
        $data = $this->getTableData($orgId, $appId);
        foreach ($data as $row) {
            if ($row['id'] == $id) {
                $action = $row['action'];
                break;
            }
        }

        if ($action === self::ACTION_DELETED) {
            return $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
                ->deleteByApplicationAndPersonId($appId, $id);
        }

        if ($action === self::ACTION_CURRENT) {
            return $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
                ->deleteByApplicationAndOriginalPersonId($appId, $id);
        }


        throw new \Exception('Can\'t restore this record');
    }

    public function savePerson($orgId, $data, $appId)
    {
        if (!empty($data['id'])) {
            return $this->update($orgId, $data, $appId);
        }

        return $this->add($orgId, $data, $appId);
    }

    private function update($orgId, $data, $appId)
    {
        $appPerson = $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
            ->getByApplicationAndPersonId($appId, $data['id']);

        if ($appPerson) {
            // save direct, that's fine...
            // @TODO: anything to update against the app_org_person table?
            return $this->getServiceLocator()->get('Entity\Person')->save($data);
        }

        /**
         * An update of an existing person means we actually create a new
         * application person which links back to the original person
         */
        $originalId = $data['id'];
        unset($data['id']);

        $newPerson = $this->getServiceLocator()->get('Entity\Person')->save($data);

        $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
            ->variationUpdate($newPerson['id'], $orgId, $appId, $originalId);
    }

    private function add($orgId, $data, $appId)
    {
        $result = $this->getServiceLocator()->get('Entity\Person')->save($data);

        $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
            ->variationCreate($result['id'], $orgId, $appId);
    }

    /**
     * Update and filter the table data for variations
     *
     * @param array $orgData
     * @param array $applicationData
     * @return array
     */
    private function updateAndFilterTableData($orgData, $applicationData)
    {
        $data = array();

        foreach ($orgData as $id => $row) {

            if (!isset($applicationData[$id])) {

                // E for existing (No updates)
                $row['action'] = self::ACTION_EXISTING;
                $data[] = $row;
            } elseif ($applicationData[$id]['action'] === self::ACTION_UPDATED) {

                $row['action'] = self::ACTION_CURRENT;
                $data[] = $row;
            }
        }

        $data = array_merge($data, $applicationData);

        return $data;
    }


    private function indexRows($key, $data)
    {
        $indexed = [];

        foreach ($data as $value) {
            // if we've got a link to an original person then that
            // trumps any other relation
            if (isset($value['originalPerson']['id'])) {
                $id = $value['originalPerson']['id'];
            } else {
                $id = $value['person']['id'];
            }
            $value['person']['source'] = $key;
            $indexed[$id] = $value;
        }

        return $indexed;
    }

    public function getPersonPosition($orgId, $appId, $personId)
    {
        $person = $this->getServiceLocator()
            ->get('Entity\ApplicationOrganisationPerson')
            ->getByApplicationAndPersonId($appId, $personId);

        if (!$person) {
            $person = $this->getServiceLocator()
                ->get('Entity\OrganisationPerson')
                ->getByOrgAndPersonId($orgId, $personId);
        }

        return $person['position'];
    }
}
