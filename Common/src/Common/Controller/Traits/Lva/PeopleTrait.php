<?php

/**
 * Shared logic between People controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Traits\Lva;

use Common\Service\Entity\OrganisationEntityService;

/**
 * Shared logic between People controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait PeopleTrait
{
    use GenericLvaTrait,
        CrudTableTrait;

    /**
     * Needed by the Crud Table Trait
     */
    private $section = 'people';

    /**
     * Index action
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $orgId = $this->getCurrentOrganisationId();
        $orgData = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getType($orgId);

        $this->populatePeople($orgId, $orgData);

        if ($request->isPost()) {
            $data = (array)$request->getPost();

            if (isset($data['table']['action'])) {
                return $this->handleCrudAction($data['table'], 'people');
            }

            return $this->completeSection('people');
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\People');

        $table = $this->getServiceLocator()
            ->get('Table')
            ->buildTable(
                'application_your-business_people_in_form',
                $this->getTableData($orgId),
                array(),
                false
            );

        $column = $table->getColumn('name');
        $column['type'] = $this->lva;
        $table->setColumn('name', $column);

        $form->get('table')  // fieldset
            ->get('table')   // element
            ->setTable($table);

        $this->alterForm($form, $table, $orgData);

        return $this->render('people', $form);
    }

    /**
     * Alter form based on company type
     */
    private function alterForm($form, $table, $orgData)
    {
        $tableHeader = 'selfserve-app-subSection-your-business-people-tableHeader';
        $guidanceLabel = 'selfserve-app-subSection-your-business-people-guidance';

        // needed in here?
        $translator = $this->getServiceLocator()->get('translator');

        switch ($orgData['type']['id']) {
        case OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY:
            $tableHeader .= 'Directors';
            $guidanceLabel .= 'LC';
            break;

        case OrganisationEntityService::ORG_TYPE_LLP:
            $tableHeader .= 'Partners';
            $guidanceLabel .= 'LLP';
            break;

        case OrganisationEntityService::ORG_TYPE_PARTNERSHIP:
            $tableHeader .= 'Partners';
            $guidanceLabel .= 'P';
            break;

        case OrganisationEntityService::ORG_TYPE_OTHER:
            $tableHeader .= 'People';
            $guidanceLabel .= 'O';
            break;

        default:
            break;
        }

        // a separate if saves repeating this three times in the switch...
        if ($orgData['type']['id'] !== OrganisationEntityService::ORG_TYPE_OTHER) {
            $table->removeColumn('position');
        }

        $table->setVariable(
            'title',
            $translator->translate($tableHeader)
        );

        $form->get('guidance')
            ->get('guidance')
            ->setValue($translator->translate($guidanceLabel));
    }

    /**
     * Add person action
     */
    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    /**
     * Edit person action
     */
    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * Helper method as both add and edit pretty
     * much do the same thing
     *
     * @param string $mode
     */
    private function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $orgId = $this->getCurrentOrganisationId();
        $orgData = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getType($orgId);

        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->formatCrudDataForForm(
                $this->getServiceLocator()->get('Entity\Person')->getById($this->params('child_id'))
            );
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\Person');

        if ($orgData['type']['id'] === OrganisationEntityService::ORG_TYPE_OTHER) {
            // we need to pre-populate the user's position from the org for
            // 'other' business types
            $data['position'] = $this->getOrganisationPosition($orgId);
        } else {
            // otherwise we're not interested in position at all, bin it off
            $this->getServiceLocator()->get('Helper\Form')
                ->remove($form, 'data->position');
        }

        $form->setData($data);

        if ($request->isPost() && $form->isValid()) {
            $data = $this->formatCrudDataForSave($data);

            $person = $this->getServiceLocator()->get('Entity\Person')->save($data);

            $this->addOrganisationPerson(
                $mode,
                $orgId,
                $orgData,
                $person,
                $data
            );

            return $this->handlePostSave();
        }

        return $this->render($mode . '_people', $form);
    }

    /**
     * Get the table data for the main form
     *
     * @param int $orgId
     * @return array
     */
    private function getTableData($orgId)
    {
        $results = $this->getServiceLocator()->get('Entity\Person')
            ->getAllForOrganisation($orgId);

        $final = array();
        foreach ($results['Results'] as $row) {
            // flatten the person's position if it's non null
            if (isset($row['position'])) {
                $row['person']['position'] = $row['position'];
            }
            $final[] = $row['person'];
        }
        return $final;
    }

    /**
     * Format data for injecting into CRUD form
     */
    private function formatCrudDataForForm($data)
    {
        return array('data' => $data);
    }

    /**
     * Format data from CRUD form
     */
    private function formatCrudDataForSave($data)
    {
        $dob = $data['data']['birthDate'];
        return array_merge(
            $data['data'],
            array(
                'birthDate' => $dob['year'] . '-' . $dob['month'] . '-' . $dob['day']
            )
        );
    }

    /**
     * Mechanism to *actually* delete a person, invoked by the
     * underlying delete action
     */
    protected function delete()
    {
        $orgId = $this->getCurrentOrganisationId();
        $id = $this->params('child_id');

        $orgPersonService = $this->getServiceLocator()
            ->get('Entity\OrganisationPerson');

        $orgPersonService->deleteByOrgAndPersonId($orgId, $id);

        $result = $orgPersonService->getByPersonId($id);

        // delete the actual person row if they no longer relate
        // to an organisation
        if (isset($result['Count']) && $result['Count'] === 0) {
            $this->getServiceLocator()->get('Entity\Person')
                ->delete($id);
        }
    }

    private function populatePeople($orgId, $orgData)
    {
        $orgTypesOnCompaniesHouse = array(
            OrganisationEntityService::ORG_TYPE_LLP,
            OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY
        );

        // If we are not a limited company or LLP just bail
        // OR if we have already added people
        // OR if we don't have a company number
        if (!in_array($orgData['type']['id'], $orgTypesOnCompaniesHouse)
            || $this->peopleAdded()
            || !preg_match('/^[A-Z0-9]{8}$/', $orgData['companyOrLlpNo'])) {
            return;
        }

        $result = $this->getServiceLocator()
            ->get('Data\CompaniesHouse')
            ->search('currentCompanyOfficers', $orgData['companyOrLlpNo']);

        if (is_array($result) && array_key_exists('Results', $result) && count($result['Results'])) {

            // @todo We need a better way to handle this, far too many rest calls could happen
            foreach ($result['Results'] as $person) {

                // Create a person
                $person = $this->getServiceLocator()
                    ->get('Entity\Person')
                    ->save($person);

                // If we have a person id
                if (isset($person['id'])) {

                    $organisationPersonData = array(
                        'organisation' => $orgId,
                        'person' => $person['id']
                    );

                    $this->getServiceLocator()
                        ->get('Entity\Person')
                        ->save($organisationPersonData);
                }
            }
        }
    }

    private function peopleAdded()
    {
        $orgId = $this->getCurrentOrganisationId();
        $results = $this->getServiceLocator()
            ->get('Entity\Person')
            ->getAllForOrganisation($orgId);

        return isset($results['Count']) && $results['Count'] > 0;
    }

    private function getOrganisationPosition($orgId)
    {
        $personId = $this->params('child_id');
        if ($personId === null) {
            return null;
        }

        $orgPerson = $this->getServiceLocator()
            ->get('Entity\OrganisationPerson')
            ->getByOrgAndPersonId($orgId, $personId);

        return $orgPerson['position'];
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

            // @TODO don't set this if the position hasn't changed
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
}
