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
    protected $tableData = [];

    private $exceptionalTypes = [
        OrganisationEntityService::ORG_TYPE_SOLE_TRADER,
        OrganisationEntityService::ORG_TYPE_PARTNERSHIP
    ];

    private $organisation;

    public function addMessages($orgId)
    {
    }

    public function alterFormForOrganisation(Form $form, $table, $orgId)
    {
    }

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId)
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

    protected function formatTableData($results)
    {
        $final = array();
        foreach ($results as $row) {
            // flatten the person's position if it's non null
            if (isset($row['position'])) {
                $row['person']['position'] = $row['position'];
            }
            // ... and action too
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

    protected function getOrganisation($orgId)
    {
        if ($this->organisation === null) {
            $this->organisation = $this->getServiceLocator()
                ->get('Entity\Organisation')
                ->getType($orgId);
        }

        return $this->organisation;
    }

    protected function getOrganisationType($orgId)
    {
        $orgData = $this->getOrganisation($orgId);

        return $orgData['type']['id'];
    }

    protected function isExceptionalOrganisation($orgId)
    {
        return $this->isExceptionalType(
            $this->getOrganisationType($orgId)
        );
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

        $orgType = $this->getOrganisationType($orgId);

        if (empty($data['id'])) {
            $orgPersonData = array(
                'organisation' => $orgId,
                'person' => $person['id'],
                'position' => isset($data['position']) ? $data['position'] : ''
            );
        } elseif ($orgType === OrganisationEntityService::ORG_TYPE_OTHER) {
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

    public function getPersonPosition($orgId, $personId)
    {
        $orgPerson = $this->getServiceLocator()
            ->get('Entity\OrganisationPerson')
            ->getByOrgAndPersonId($orgId, $personId);

        return $orgPerson['position'];
    }
}
