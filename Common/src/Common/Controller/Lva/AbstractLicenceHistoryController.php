<?php

/**
 * Licence History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Licence History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLicenceHistoryController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $sections = array(
        'current' => 'prevHasLicence',
        'applied' => 'prevHadLicence',
        'refused' => 'prevBeenRefused',
        'revoked' => 'prevBeenRevoked',
        'public-inquiry' => 'prevBeenAtPi',
        'disqualified' => 'prevBeenDisqualifiedTc',
        'held' => 'prevPurchasedAssets'
    );

    protected $section = 'licence_history';

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getFormData());
        }

        $form = $this->getLicenceHistoryForm()->setData($data);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction(array());

            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {

                $this->save($data);
                $this->postSave('licence_history');

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('licence_history');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFiles(['lva-crud', 'licence-history']);

        return $this->render('licence_history', $form);
    }

    /**
     * Override the get crud action method
     *
     * @param array $formTables
     * @return array
     */
    protected function getCrudAction(array $formTables = array())
    {
        $data = (array)$this->getRequest()->getPost();

        foreach (array_keys($this->sections) as $section) {

            if (isset($data[$section]['table']['action'])) {

                $action = $this->getActionFromCrudAction($data[$section]['table']);

                $data[$section]['table']['routeAction'] = $section . '-' . strtolower($action);

                return $data[$section]['table'];
            }
        }

        return null;
    }

    protected function delete()
    {
        $ids = explode(',', $this->params('child_id'));

        $service = $this->getServiceLocator()->get('Entity\PreviousLicence');

        foreach ($ids as $id) {
            $service->delete($id);
        }
    }

    protected function save($data)
    {
        $data = $this->formatDataForSave($data);

        $data['id'] = $this->getApplicationId();

        $this->getServiceLocator()->get('Entity\Application')->save($data);
    }

    protected function formatDataForSave($data)
    {
        $saveData = array();

        foreach ($this->sections as $reference => $actual) {
            if (isset($data[$reference]['question'])) {
                $saveData[$actual] = $data[$reference]['question'];
            }
        }

        $saveData['version'] = $data['current']['version'];

        return $saveData;
    }

    protected function getFormData()
    {
        return $this->getServiceLocator()->get('Entity\Application')->getLicenceHistoryData($this->getApplicationId());
    }

    protected function formatDataForForm($data)
    {
        $formData = array();

        foreach ($this->sections as $reference => $actual) {
            $formData[$reference] = array(
                'question' => $data[$actual]
            );
        }

        $formData['current']['version'] = $data['version'];

        return $formData;
    }

    protected function getLicenceHistoryForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\LicenceHistory');

        foreach (array_keys($this->sections) as $section) {
            $formHelper->populateFormTable(
                $form->get($section)->get('table'),
                $this->getTable($section),
                $section . '[table]'
            );
        }

        return $form;
    }

    protected function getTable($which)
    {
        return $this->getServiceLocator()->get('Table')
            ->prepareTable('lva-licence-history-' . $which, $this->getTableData($which));
    }

    protected function getTableData($which)
    {
        return $this->getServiceLocator()->get('Entity\PreviousLicence')
            ->getForApplicationAndType($this->getApplicationId(), $this->getLicenceTypeFromSection($which));
    }

    protected function getLicenceTypeFromSection($section)
    {
        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        return $stringHelper->camelToUnderscore($this->sections[$section]);
    }

    /**
     * Add current licence
     */
    public function currentAddAction()
    {
        return $this->addOrEdit('add', 'current');
    }

    /**
     * Edit current licence
     */
    public function currentEditAction()
    {
        return $this->addOrEdit('edit', 'current');
    }

    /**
     * Delete current licence
     */
    public function currentDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add applied licence
     */
    public function appliedAddAction()
    {
        return $this->addOrEdit('add', 'applied');
    }

    /**
     * Edit applied licence
     */
    public function appliedEditAction()
    {
        return $this->addOrEdit('edit', 'applied');
    }

    /**
     * Delete applied licence
     */
    public function appliedDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add refused licence
     */
    public function refusedAddAction()
    {
        return $this->addOrEdit('add', 'refused');
    }

    /**
     * Edit refused licence
     */
    public function refusedEditAction()
    {
        return $this->addOrEdit('edit', 'refused');
    }

    /**
     * Delete refused licence
     */
    public function refusedDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add revoked licence
     */
    public function revokedAddAction()
    {
        return $this->addOrEdit('add', 'revoked');
    }

    /**
     * Edit revoked licence
     */
    public function revokedEditAction()
    {
        return $this->addOrEdit('edit', 'revoked');
    }

    /**
     * Delete revoked licence
     */
    public function revokedDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add disqualified licence
     */
    public function disqualifiedAddAction()
    {
        return $this->addOrEdit('add', 'disqualified');
    }

    /**
     * Edit disqualified licence
     */
    public function disqualifiedEditAction()
    {
        return $this->addOrEdit('edit', 'disqualified');
    }

    /**
     * Delete disqualified licence
     */
    public function disqualifiedDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add held licence
     */
    public function heldAddAction()
    {
        return $this->addOrEdit('add', 'held');
    }

    /**
     * Edit held licence
     */
    public function heldEditAction()
    {
        return $this->addOrEdit('edit', 'held');
    }

    /**
     * Delete held licence
     */
    public function heldDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add public inquiry licence
     */
    public function publicInquiryAddAction()
    {
        return $this->addOrEdit('add', 'public-inquiry');
    }

    /**
     * Edit public inquiry licence
     */
    public function publicInquiryEditAction()
    {
        return $this->addOrEdit('edit', 'public-inquiry');
    }

    /**
     * Delete public inquiry licence
     */
    public function publicInquiryDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Generic functionality for adding/editing
     *
     * @param string $mode
     * @param string $which
     * @return mixed
     */
    protected function addOrEdit($mode, $which)
    {
        $request = $this->getRequest();

        $data = array();
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $id = $this->params('child_id');

            $data = $this->getLicenceFormData($id);

            // If the loaded previous licence type doesn't match the one we are editing
            if ($data['previousLicenceType']['id'] !== $this->getLicenceTypeFromSection($which)) {
                return $this->notFoundAction();
            }
        }

        if (!$request->isPost()) {
            $data = $this->formatDataForLicenceForm($data, $which);
        }

        $form = $this->alterActionForm($this->getLicenceForm(), $which)->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $this->saveLicence($data);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_licence_history', $form);
    }

    /**
     * Get the altered licence form
     *
     * @param string $which
     * @return \Zend\Form\Form
     */
    protected function getLicenceForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\LicenceHistoryLicence');
    }

    /**
     * Alter the form based on the licence type
     *
     * @param \Zend\Form\Form $form
     * @param string $which
     * @return \Zend\Form\Form
     */
    protected function alterActionForm($form, $which)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        if ($which !== 'disqualified') {
            $formHelper->remove($form, 'data->disqualificationDate');
            $formHelper->remove($form, 'data->disqualificationLength');
        }

        if ($which !== 'current') {
            $formHelper->remove($form, 'data->willSurrender');
        }

        if ($which !== 'held') {
            $formHelper->remove($form, 'data->purchaseDate');
        }

        return $form;
    }

    protected function getLicenceFormData($id)
    {
        return $this->getServiceLocator()->get('Entity\PreviousLicence')->getById($id);
    }

    /**
     * Process action load
     *
     * @param $data
     */
    protected function formatDataForLicenceForm($data, $which)
    {
        $data['previousLicenceType'] = $this->getLicenceTypeFromSection($which);

        return array(
            'data' => $data
        );
    }

    /**
     * Save licenceR
     *
     * @param array $data
     * @param string $service
     */
    protected function saveLicence($data)
    {
        $saveData = $data['data'];

        if (isset($saveData['disqualificationDate'])) {
            $saveData['disqualificationDate'] = sprintf(
                '%s-%s-%s',
                $saveData['disqualificationDate']['year'],
                $saveData['disqualificationDate']['month'],
                $saveData['disqualificationDate']['day']
            );
        }

        if (isset($saveData['purchaseDate'])) {
            $saveData['purchaseDate'] = sprintf(
                '%s-%s-%s',
                $saveData['purchaseDate']['year'],
                $saveData['purchaseDate']['month'],
                $saveData['purchaseDate']['day']
            );
        }

        $saveData['id'] = $this->params('child_id');

        if (empty($saveData['id'])) {
            unset($saveData['id']);
            unset($saveData['version']);
        }

        $saveData['application'] = $this->getApplicationId();

        $this->getServiceLocator()->get('Entity\PreviousLicence')->save($saveData);
    }
}
