<?php

/**
 * Shared logic between People controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\OrganisationEntityService;
use Common\Controller\Lva\Interfaces\AdapterAwareInterface;

/**
 * Shared logic between People controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractPeopleController extends AbstractController implements AdapterAwareInterface
{
    use Traits\AdapterAwareTrait,
        Traits\CrudTableTrait {
        Traits\CrudTableTrait::deleteAction as originalDeleteAction;
    }


    /**
     * Needed by the Crud Table Trait
     */
    protected $section = 'people';

    /**
     * Index action
     */
    public function indexAction()
    {
        $adapter = $this->getAdapter();
        $orgId = $this->getCurrentOrganisationId();
        $orgData = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getType($orgId);

        $adapter->addMessages($orgId);

        if ($orgData['type']['id'] === OrganisationEntityService::ORG_TYPE_SOLE_TRADER) {
            return $this->handleSoleTrader($orgId, $orgData);
        }

        /**
         * Could bung this in another method, but since it's everything other
         * than sole trader, it makes no real difference
         */

        $request = $this->getRequest();

        $this->populatePeople($orgId, $orgData);

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $this->postSave('people');

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }

            return $this->completeSection('people');
        }

        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\People');

        $table = $this->getServiceLocator()->get('Table')->prepareTable('lva-people', $this->getTableData($orgId));

        $form->get('table')
            ->get('table')
            ->setTable($table);

        $this->alterForm($form, $table, $orgData);

        if ($orgData['type']['id'] === OrganisationEntityService::ORG_TYPE_PARTNERSHIP) {
            $adapter->alterFormForPartnership($form, $table, $orgId);
        } else {
            $adapter->alterFormForOrganisation($form, $table, $orgId);
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('people', $form);
    }

    private function handleSoleTrader($orgId, $orgData)
    {
        $adapter = $this->getAdapter();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $person = $this->getServiceLocator()
                ->get('Entity\Person')
                ->getFirstForOrganisation($orgId);
            $data = $this->formatCrudDataForForm($person);
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper
            ->createForm('Lva\SoleTrader')
            ->setData($data);

        $this->alterFormForLva($form);

        $adapter->alterSoleTraderFormForOrganisation($form, $orgId);

        if ($request->isPost() && $form->isValid()) {
            $data = $this->formatCrudDataForSave($form->getData());
            $person = $this->getServiceLocator()->get('Entity\Person')->save($data);

            if (!$data['id']) {
                $this->addOrganisationPerson('add', $orgId, $orgData, $person, $data);
            }

            $this->postSave('people');
            return $this->completeSection('people');
        }

        return $this->render('person', $form);
    }

    /**
     * Alter form based on company type
     */
    private function alterForm($form, $table, $orgData)
    {
        $this->alterFormForLva($form);

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
        $orgId = $this->getCurrentOrganisationId();

        if (!$this->getAdapter()->canModify($orgId)) {
            return $this->redirectWithoutPermission();
        }

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
        $adapter = $this->getAdapter();
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
            ->createFormWithRequest('Lva\Person', $request);

        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        if ($orgData['type']['id'] === OrganisationEntityService::ORG_TYPE_OTHER) {
            // we need to pre-populate the user's position from the org for
            // 'other' business types
            $data['position'] = $this->getOrganisationPosition($orgId);
        } else {
            // otherwise we're not interested in position at all, bin it off
            $this->getServiceLocator()->get('Helper\Form')
                ->remove($form, 'data->position');
        }

        $adapter->alterAddOrEditFormForOrganisation($form, $orgId);

        $form->setData($data);

        if ($request->isPost() && $form->isValid()) {
            $data = $this->formatCrudDataForSave($form->getData());

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
        return array_filter(
            $data['data'],
            function ($v) {
                return $v !== null;
            }
        );
    }

    /**
     * Mechanism to *actually* delete a person, invoked by the
     * underlying delete action
     */
    protected function delete()
    {
        $id = $this->params('child_id');
        $orgId = $this->getCurrentOrganisationId();

        // This allows us to handle multiple delete
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            $this->deletePerson($id, $orgId);
        }
    }

    /**
     * Delete a person from the organisation, and then delete the person if they are now an orphan
     *
     * @param int $id
     */
    protected function deletePerson($id, $orgId)
    {
        $orgPersonService = $this->getServiceLocator()->get('Entity\OrganisationPerson');

        $orgPersonService->deleteByOrgAndPersonId($orgId, $id);

        $result = $orgPersonService->getByPersonId($id);

        // delete the actual person row if they no longer relate
        // to an organisation
        if (isset($result['Count']) && $result['Count'] === 0) {
            $this->getServiceLocator()->get('Entity\Person')->delete($id);
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

        if (!isset($result['Results'])) {
            return;
        }

        // @todo We need a better way to handle this, far too many rest calls could happen
        // multi create would help; one call to create all people, another to create
        // all relations to org person
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
                    ->get('Entity\OrganisationPerson')
                    ->save($organisationPersonData);
            }
        }
    }

    private function peopleAdded()
    {
        $orgId = $this->getCurrentOrganisationId();
        $results = $this->getServiceLocator()
            ->get('Entity\Person')
            ->getAllForOrganisation($orgId, 1);

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

    /**
     * Delete person action
     */
    public function deleteAction()
    {
        $orgId = $this->getCurrentOrganisationId();

        if (!$this->getAdapter()->canModify($orgId)) {
            return $this->redirectWithoutPermission();
        }

        return $this->originalDeleteAction();
    }

    private function redirectWithoutPermission()
    {
        $this->addErrorMessage('cannot-perform-action');
        return $this->redirect()->toRouteAjax(
            null,
            [$this->getIdentifierIndex() => $this->getIdentifier()]
        );
    }
}
