<?php

/**
 * Common (aka Internal) Application People Adapter
 *
 * @NOTE: currently identical and not abstracted at all
 * in common between all three LVAs
 *
 * This will change in the next story to be developed
 * OLCS-6543
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;

/**
 * Common (aka Internal) Variation People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VariationPeopleAdapter extends AbstractPeopleAdapter
{
    protected $lva = 'variation';

    public function alterFormForOrganisation(Form $form, $table, $orgId, $orgType)
    {
        // internally we don't ever need to alter the index form
        // this is because partnerships can always edit straight up,
        // and other types like LTD, LLP make their alterations elsewhere
    }

    public function createTable($orgId)
    {
        // @TODO perhaps make a getTableConfig($orgId) mechanism
        if (!$this->isExceptionalOrganisation($orgId)) {
            $this->tableConfig = 'lva-variation-people';
        }
        return parent::createTable($orgId);
    }

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId, $orgType)
    {
    }

    public function canModify($orgId)
    {
        return true;
    }

    // @TODO: all methods below are duplicated across int/ext
    // variation adapters
    // I don't think inheritance is the solution, so either wrap
    // another service or... something else

    public function attachMainScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud-delta');
    }

    /**
     * Extend the abstract behaviour to get the table data for the main form
     *
     * @return array
     */
    protected function getTableData($orgId)
    {
        if (empty($this->tableData)) {

            $orgPeople = $this->getServiceLocator()->get('Entity\Person')
                ->getAllForOrganisation($orgId)['Results'];

            $applicationPeople = $this->getServiceLocator()->get('Entity\Person')
                ->getAllForApplication($this->getVariationAdapter()->getIdentifier())['Results'];

            $data = $this->updateAndFilterTableData(
                $this->indexRows(self::SOURCE_ORGANISATION, $orgPeople),
                $this->indexRows(self::SOURCE_APPLICATION, $applicationPeople)
            );

            $this->tableData = $this->formatTableData($data);
        }

        return $this->tableData;
    }

    /**
     * Update and filter the table data for variations
     *
     * @param array $orgData
     * @param array $applicationData
     * @return array
     */
    protected function updateAndFilterTableData($orgData, $applicationData)
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

    public function delete($orgId, $id)
    {
        $appId = $this->getLvaAdapter()->getIdentifier();

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

    public function restore($orgId, $id)
    {
        // @TODO methodize
        $data = $this->getTableData($orgId);
        foreach ($data as $row) {
            if ($row['id'] == $id) {
                $action = $row['action'];
                break;
            }
        }

        $appId = $this->getLvaAdapter()->getIdentifier();

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

    public function save($orgId, $data)
    {
        if (!empty($data['id'])) {
            return $this->update($orgId, $data);
        }

        return $this->add($orgId, $data);
    }

    private function update($orgId, $data)
    {
        $appId = $this->getLvaAdapter()->getIdentifier();

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

    private function add($orgId, $data)
    {
        $appId = $this->getLvaAdapter()->getIdentifier();

        $result = $this->getServiceLocator()->get('Entity\Person')->save($data);

        $this->getServiceLocator()->get('Entity\ApplicationOrganisationPerson')
            ->variationCreate($result['id'], $orgId, $appId);
    }
}
