<?php

/**
 * Abstract people adapter
 *
 * Contains common logic which used to just live in the abstract
 * people controller, i.e. the "plain" unmodified behaviour
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Interfaces\PeopleAdapterInterface;
use Common\Service\Entity\OrganisationEntityService;

/**
 * Abstract people adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractPeopleAdapter extends AbstractControllerAwareAdapter implements PeopleAdapterInterface
{
    const ACTION_ADDED = 'A';
    const ACTION_EXISTING = 'E';
    const ACTION_CURRENT = 'C';
    const ACTION_UPDATED = 'U';
    const ACTION_DELETED = 'D';
    const SOURCE_APPLICATION = 'A';
    const SOURCE_ORGANISATION = 'O';

    protected $tableData = [];

    private $exceptionalTypes = [
        OrganisationEntityService::ORG_TYPE_SOLE_TRADER,
        OrganisationEntityService::ORG_TYPE_PARTNERSHIP
    ];

    public function addMessages($orgType)
    {
    }

    public function alterFormForOrganisation(Form $form, $table, $orgId, $orgType)
    {
    }

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId, $orgType)
    {
    }

    public function canModify($orgId)
    {
        return true;
    }

    public function createTable($orgId)
    {
        return $this->getServiceLocator()
            ->get('Table')
            ->prepareTable($this->getTableConfig($orgId), $this->getTableData($orgId));
    }

    /**
     * Get the table data for the main form
     *
     * @param int $orgId
     * @return array
     */
    protected function getTableData($orgId)
    {
        if (empty($this->tableData)) {
            $results = $this->getServiceLocator()->get('Entity\Person')
                ->getAllForOrganisation($orgId)['Results'];

            $this->tableData = $this->formatTableData($results);
        }
        return $this->tableData;
    }

    public function attachMainScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');
    }

    protected function formatTableData($results)
    {
        $final = array();
        foreach ($results as $row) {
            // flatten the person's position if it's non null
            if (isset($row['position'])) {
                $row['person']['position'] = $row['position'];
            }
            // @TODO: move this out into the variation formatTableData
            // call perhaps? Then again, all this stuff is a bit nasty...
            if (isset($row['action'])) {
                $row['person']['action'] = $row['action'];
            }
            $final[] = $row['person'];
        }
        return $final;
    }

    protected function isExceptionalType($orgType)
    {
        return in_array($orgType, $this->exceptionalTypes);
    }

    protected function isExceptionalOrganisation($orgId)
    {
        $orgData = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getType($orgId);

        return $this->isExceptionalType($orgData['type']['id']);
    }

    /**
     * Delete a person from the organisation, and then delete the person if they are now an orphan
     *
     * @param int $id
     */
    public function delete($orgId, $id)
    {
        $orgPersonService = $this->getServiceLocator()->get('Entity\OrganisationPerson');

        $orgPersonService->deleteByOrgAndPersonId($orgId, $id);

        $result = $orgPersonService->getAllWithPerson($id);

        // delete the actual person row if they no longer relate
        // to an organisation
        if (isset($result['Count']) && $result['Count'] === 0) {
            $this->getServiceLocator()->get('Entity\Person')->delete($id);
        }
    }

    public function restore($orgId, $id)
    {
        throw new \Exception('Not implemented');
    }

    public function save($orgId, $data)
    {
        $person = $this->getServiceLocator()->get('Entity\Person')->save($data);

        $this->addOrganisationPerson(
            $mode,
            $orgId,
            $orgData,
            $person,
            $data
        );
    }

    /**
     * Helper method to conditionally add or update a matching organisation
     * person record when saving a new person
     */
    private function addOrganisationPerson($mode, $orgId, $orgData, $person, $data)
    {
        // If we are creating a person, we need to link them to the organisation,
        // otherwise we might need to update person's position
        if ($mode === 'add') {
            $orgPersonData = array(
                'organisation' => $orgId,
                'person' => $person['id'],
                'position' => isset($data['position']) ? $data['position'] : ''
            );
        } elseif ($orgData['type']['id'] === OrganisationEntityService::ORG_TYPE_OTHER) {
            $orgPerson = $this->getServiceLocator()
                ->get('Entity\OrganisationPerson')
                ->getByOrgAndPersonId($orgId, $data['id']);

            $orgPersonData = array(
                'position' => isset($data['position']) ? $data['position'] : '',
                'id' => $orgPerson['id'],
                'version' => $orgPerson['version'],
            );
        }

        if (isset($orgPersonData)) {
            $this->getServiceLocator()->get('Entity\OrganisationPerson')->save($orgPersonData);
        }
    }

    protected function getTableConfig($orgId)
    {
        return 'lva-people';
    }
}
