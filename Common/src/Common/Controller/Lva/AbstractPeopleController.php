<?php

namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;
use Common\RefData;

/**
 * Shared logic between People controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractPeopleController extends AbstractController implements AdapterAwareInterface
{
    use Traits\AdapterAwareTrait,
        Traits\CrudTableTrait;

    /**
     * Needed by the Crud Table Trait
     */
    protected $section = 'people';
    protected $baseRoute = 'lva-%s/people';

    /**
     * Index action
     */
    public function indexAction()
    {
        /* @var $adapter Adapters\AbstractPeopleAdapter */
        $this->getAdapter()->loadPeopleData($this->lva, $this->getIdentifier());

        if ($this->location === 'external') {
            $this->addGuidanceMessage();
        }

        if ($this->getAdapter()->isSoleTrader()) {
            return $this->handleSoleTrader();
        }

        return $this->handleNonSoleTrader();
    }

    /**
     * Handle all except sole trader
     */
    private function handleNonSoleTrader()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = (array)$request->getPost();

            $crudAction = $this->getCrudAction(array($postData['table']));

            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }
            $this->postSaveCommands();

            return $this->completeSection('people');
        }

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();
        // @todo move alterForm and alterFormForOrganisation logic into form services

        $table = $this->getAdapter()->createTable();

        $form->get('table')
            ->get('table')
            ->setTable($table);

        $this->alterForm($form, $table, $this->getAdapter()->getOrganisationType());

        $this->getAdapter()->alterFormForOrganisation($form, $table);

        $this->getServiceLocator()->get('Script')->loadFiles(['lva-crud-delta', 'more-actions']);

        return $this->render('people', $form);
    }

    /**
     * Handle indexAction if a sole trader
     */
    private function handleSoleTrader()
    {
        /* @var $adapter Adapters\AbstractPeopleAdapter */
        $adapter = $this->getAdapter();

        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        /** @var array $personData */
        $personData = $adapter->getFirstPersonData();
        if ($request->isPost()) {
            $data = (array) $request->getPost();
        } else {
            if ($personData === false) {
                $data['data'] = [];
            } else {
                $data['data'] = $personData['person'];
                $data['data']['position'] = $personData['position'];
            }
        }

        $params = [
            'location' => $this->location,
            'canModify' => $adapter->canModify(),
            'orgType' => $adapter->getOrganisationType()
        ];

        if ($this->location === 'internal') {
            $personId = (isset($personData['person']['id'])) ? $personData['person']['id'] : null;

            $params['disqualifyUrl'] = $this->url()->fromRoute(
                'operator/disqualify_person',
                ['organisation' => $adapter->getOrganisationId(), 'person' => $personId]
            );
            $params['isDisqualified'] = $this->isPersonDisqualified($personData);
            $params['personId'] = $personId;
        }

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-sole_trader')
            ->getForm($params);

        $form->setData($data);

        $this->alterCrudForm($form, 'edit', $adapter->getOrganisation());

        if ($request->isPost() && $form->isValid()) {
            $data = $this->formatCrudDataForSave($form->getData());

            if ($form->getAttribute('locked') !== true) {
                $this->savePerson($data);
                $this->postSaveCommands();
            } else {
                $this->updateCompletion();
            }

            return $this->completeSection('people');
        }

        return $this->render('person', $form);
    }

    protected function postSaveCommands()
    {
        $this->updateCompletion();

        if ($this->lva === 'application' && $this->location === 'external') {
            $this->handleCommand(
                \Dvsa\Olcs\Transfer\Command\Application\GenerateOrganisationName::create(
                    ['id' => $this->getIdentifier()]
                )
            );
        }
    }

    protected function updateCompletion()
    {
        if ($this->lva != 'licence') {
            $this->handleCommand(
                \Dvsa\Olcs\Transfer\Command\Application\UpdateCompletion::create(
                    ['id' => $this->getIdentifier(), 'section' => 'people']
                )
            );
        }
    }

    private function savePerson($data)
    {
        if (empty($data['id'])) {
            $this->getAdapter()->create($data);
        } else {
            $this->getAdapter()->update($data);
        }
    }

    /**
     * Alter form based on company type
     */
    private function alterForm($form, \Common\Service\Table\TableBuilder $table, $organisationTypeId)
    {
        $this->alterFormForLva($form);

        $tableHeader = 'selfserve-app-subSection-your-business-people-tableHeader';

        switch ($organisationTypeId) {
            case RefData::ORG_TYPE_REGISTERED_COMPANY:
                $tableHeader .= 'Directors';
                break;

            case RefData::ORG_TYPE_LLP:
                $tableHeader .= 'PartnersMembers';
                break;

            case RefData::ORG_TYPE_PARTNERSHIP:
                $tableHeader .= 'Partners';
                break;

            case RefData::ORG_TYPE_OTHER:
                $tableHeader .= 'People';
                break;

            default:
                break;
        }

        // if not on internal then remove the disqual column
        if ($this->location !== 'internal') {
            $table->removeColumn('disqual');
        }

        // a separate if saves repeating this three times in the switch...
        if ($organisationTypeId !== RefData::ORG_TYPE_OTHER) {
            $table->removeColumn('position');
        }

        if ($this->isExternal() && $this->lva === 'licence' && $table->getTotal() == 0) {
            $form->remove('table');
        }

        $table->setVariable(
            'title',
            $this->getServiceLocator()->get('translator')->translate($tableHeader)
        );
    }

    private function addGuidanceMessage()
    {
        $guidanceLabel = 'selfserve-app-subSection-your-business-people-guidance';
        switch ($this->getAdapter()->getOrganisationType()) {
            case RefData::ORG_TYPE_REGISTERED_COMPANY:
                $guidanceLabel .= 'LC';
                break;
            case RefData::ORG_TYPE_LLP:
                $guidanceLabel .= 'LLP';
                break;
            case RefData::ORG_TYPE_PARTNERSHIP:
                $guidanceLabel .= 'P';
                break;
            case RefData::ORG_TYPE_OTHER:
                $guidanceLabel .= 'O';
                break;
            default:
                $guidanceLabel = null;
        }

        if ($this->getAdapter()->canModify()) {
            if ($guidanceLabel !== null) {
                $this->getServiceLocator()->get('Helper\Guidance')->append($guidanceLabel);
            }
        } else {
            if ($this->lva === 'licence' &&
                (
                    ($this->getAdapter()->isOrganisationLimited() &&
                        $this->getAdapter()->getLicenceType() !== \Common\RefData::LICENCE_TYPE_SPECIAL_RESTRICTED)
                    || $this->getAdapter()->isOrganisationOther()
                )
            ) {
                $this->getServiceLocator()->get('Lva\Variation')->addVariationMessage($this->getLicenceId(), 'people');
            } else {
                $this->getServiceLocator()->get('Helper\Guidance')->append(
                    'selfserve-app-subSection-your-business-people-guidance-disabled'
                );
            }
        }
    }

    private function alterCrudForm($form, $mode, $orgData)
    {
        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        $personData = $this->getAdapter()->getFirstPersonData();
        $personId = (isset($personData['person']['id'])) ? $personData['person']['id'] : null;
        // if not internal OR no  person OR already disqualified then hide the disqualify button

        if ($this->location !== 'internal' ||
            empty($personId) ||
            $this->isPersonDisqualified($personData) ||
            !$this->getAdapter()->isSoleTrader()
        ) {
            $this->getServiceLocator()->get('Helper\Form')->remove($form, 'form-actions->disqualify');
        } else {
            $form->get('form-actions')->get('disqualify')->setValue(
                $this->url()->fromRoute(
                    'operator/disqualify_person',
                    ['organisation' => $this->getAdapter()->getOrganisationId(), 'person' => $personId]
                )
            );
        }

        if ($orgData['type']['id'] !== RefData::ORG_TYPE_OTHER) {
            // otherwise we're not interested in position at all, bin it off
            $this->getServiceLocator()->get('Helper\Form')
                ->remove($form, 'data->position');
        }
    }

    /**
     * Add person action
     */
    public function addAction()
    {
        /* @var $adapter Adapters\AbstractPeopleAdapter */
        $adapter = $this->getAdapter();
        $adapter->loadPeopleData($this->lva, $this->getIdentifier());
        if (!$adapter->canModify()) {
            return $this->redirectWithoutPermission();
        }

        return $this->addOrEdit('add');
    }

    /**
     * Edit person action
     */
    public function editAction()
    {
        /* @var $adapter Adapters\AbstractPeopleAdapter */
        $adapter = $this->getAdapter();
        $adapter->loadPeopleData($this->lva, $this->getIdentifier());
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
        /* @var $adapter Adapters\AbstractPeopleAdapter */
        $adapter = $this->getAdapter();
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        $data = array();
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $personId = (int) $this->params('child_id');
            $personData = $adapter->getPersonData($personId);
            $data['data'] = $personData['person'];
            $data['data']['position'] = $personData['position'];
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\Person', $request);

        $this->alterCrudForm($form, $mode, $adapter->getOrganisation());

        $adapter->alterAddOrEditFormForOrganisation($form);

        $form->setData($data);

        if ($request->isPost() && $form->isValid()) {
            $data = $this->formatCrudDataForSave($form->getData());

            $this->savePerson($data);

            return $this->handlePostSave(null, false);
        }

        return $this->render($mode . '_people', $form);
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
        /* @var $adapter Adapters\AbstractPeopleAdapter */
        $adapter = $this->getAdapter();
        $adapter->loadPeopleData($this->lva, $this->getIdentifier());
        if (!$adapter->canModify()) {
            return $this->redirectWithoutPermission();
        }
        $id = $this->params('child_id');
        $ids = explode(',', $id);

        $adapter->delete($ids);
    }

    private function redirectWithoutPermission()
    {
        $this->addErrorMessage('cannot-perform-action');
        return $this->redirect()->toRouteAjax(
            null,
            [$this->getIdentifierIndex() => $this->getIdentifier()]
        );
    }

    public function restoreAction()
    {
        /* @var $adapter Adapters\AbstractPeopleAdapter */
        $adapter = $this->getAdapter();
        $adapter->loadPeopleData($this->lva, $this->getIdentifier());
        $id = $this->params('child_id');
        $ids = explode(',', $id);
        $adapter->restore($ids);

        return $this->redirect()->toRouteAjax(
            null,
            [$this->getIdentifierIndex() => $this->getIdentifier()]
        );
    }

    /**
     * Is the person in the personData disqualified
     *
     * @param array $personData
     * @return boolean
     */
    protected function isPersonDisqualified($personData)
    {
        if (isset($personData['person']['disqualificationStatus'])) {
            return $personData['person']['disqualificationStatus'] !== 'None';
        }
        return false;
    }
}
