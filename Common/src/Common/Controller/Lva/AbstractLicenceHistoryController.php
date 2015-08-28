<?php

/**
 * Licence History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Dvsa\Olcs\Transfer\Command\Application\UpdateLicenceHistory;
use Dvsa\Olcs\Transfer\Command\OtherLicence\UpdateOtherLicence;
use Dvsa\Olcs\Transfer\Command\OtherLicence\CreateOtherLicence;
use Dvsa\Olcs\Transfer\Command\OtherLicence\DeleteOtherLicence;
use Dvsa\Olcs\Transfer\Query\OtherLicence\OtherLicence;
use Common\Data\Mapper\Lva\OtherLicence as OtherLicenceMapper;
use Common\Data\Mapper\Lva\LicenceHistory as LicenceHistoryMapper;
use Dvsa\Olcs\Transfer\Query\Application\LicenceHistory;

/**
 * Licence History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
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

    protected $otherLicences = [];

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getFormData());
        }

        $form = $this->getLicenceHistoryForm()->setData($data);

        $this->alterFormForLva($form);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction($data);

            $inProgress = false;
            if ($crudAction !== null) {
                $inProgress = true;
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid() && $this->saveLicenceHistory($form, $data, $inProgress)) {

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
        $data = $formTables;

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
        $saveData = [
            'ids' => explode(',', $this->params('child_id'))
        ];
        $dto = DeleteOtherLicence::create($saveData);

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);
        if ($response->isOk()) {
            return true;
        }

        if ($response->isServerError() || $response->isClientError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }
    }

    protected function saveLicenceHistory($form, $data, $inProgress)
    {
        $data = $this->formatDataForSave($data);

        $data['id'] = $this->getApplicationId();
        $data['inProgress'] = $inProgress;

        $dto = UpdateLicenceHistory::create($data);

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);
        if ($response->isOk()) {
            return true;
        }
        if ($response->isClientError()) {
            $fieldsets = [
                'prevHasLicence' => 'current',
                'prevHadLicence' => 'applied',
                'prevBeenRefused' => 'refused',
                'prevBeenRevoked' => 'revoked',
                'prevBeenAtPi' => 'held',
                'prevBeenDisqualifiedTc' => 'disqualified'
            ];
            $this->mapErrorsForLicenceHistory($form, $response->getResult()['messages'], $fieldsets);
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        return false;
    }

    protected function mapErrorsForLicenceHistory($form, array $errors, array $fieldsets = [])
    {
        $formMessages = [];

        foreach ($fieldsets as $errorKey => $fieldsetName) {
            if (isset($errors[$errorKey])) {
                foreach ($errors[$errorKey] as $key => $message) {
                    $formMessages[$fieldsetName]['question'][] = $message;
                }

                unset($errors[$key]);
            }
        }

        $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
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
        $response = $this->getLicenceHistory();

        if ($response->isNotFound()) {
            return $this->notFoundAction();
        }

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        $mappedResults = [];
        if ($response->isOk()) {
            $mapper = new LicenceHistoryMapper();
            $mappedResults = $mapper->mapFromResult($response->getResult());
            $this->otherLicences = $mappedResults['data']['otherLicences'];
        }
        return $mappedResults;
    }

    /**
     * @return \Common\Service\Cqrs\Response
     */
    protected function getLicenceHistory()
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(LicenceHistory::create(['id' => $this->getIdentifier()]));

        return $this->getServiceLocator()->get('QueryService')->send($query);
    }

    protected function formatDataForForm($data)
    {
        $data = $data['data'];
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

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();

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
        if (!count($this->otherLicences)) {
            $this->getFormData();
        }
        return $this->otherLicences[$which];
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
        return $this->deleteAction(false);
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
        return $this->deleteAction(false);
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
        return $this->deleteAction(false);
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
        return $this->deleteAction(false);
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
        return $this->deleteAction(false);
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
        return $this->deleteAction(false);
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
        return $this->deleteAction(false);
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

        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        if ($request->isPost() && $form->isValid()) {

            $this->saveLicence($form, $form->getData());

            return $this->handlePostSave($which, false);
        }

        return $this->render($mode . '_licence_history', $form);
    }

    /**
     * Get licence form data
     *
     * @param int $id
     * @return array
     */
    protected function getLicenceFormData($id)
    {
        $response = $this->getOtherLicenceData($id);

        if ($response->isNotFound()) {
            return $this->notFoundAction();
        }

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        $mappedResults = [];
        if ($response->isOk()) {
            $mapper = new OtherLicenceMapper();
            $mappedResults = $mapper->mapFromResult($response->getResult());
        }
        return $mappedResults;
    }

    protected function getOtherLicenceData($id)
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(OtherLicence::create(['id' => $id]));

        return $this->getServiceLocator()->get('QueryService')->send($query);
    }

    /**
     * Get the altered licence form
     *
     * @return \Zend\Form\Form
     */
    protected function getLicenceForm()
    {
        return $this->getServiceLocator()
            ->get('Helper\Form')
            ->createFormWithRequest('Lva\LicenceHistoryLicence', $this->getRequest());
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
     * Save licence
     *
     * @param Olcs\Common\Form $form
     * @param array $formData
     */
    protected function saveLicence($form, $formData)
    {
        $saveData = $formData['data'];
        $saveData['id'] = $this->params('child_id');
        $saveData['application'] = $this->getApplicationId();
        if (empty($saveData['id'])) {
            unset($saveData['id']);
            unset($saveData['version']);
            $dto = CreateOtherLicence::create($saveData);
        } else {
            $dto = UpdateOtherLicence::create($saveData);
        }

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);
        if ($response->isOk()) {
            return true;
        }

        if ($response->isClientError()) {
            $fields = [
                'licNo' => 'licNo',
                'holderName' => 'holderName',
                'willSurrender' => 'willSurrender',
                'disqualificationDate' => 'disqualificationDate',
                'disqualificationLength' => 'disqualificationLength',
                'purchaseDate' => 'purchaseDate',
            ];
            $this->mapErrors($form, $response->getResult()['messages'], $fields, 'data');
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }
        return false;
    }

    protected function mapErrors($form, array $errors, array $fields = [], $fieldsetName = '')
    {
        $formMessages = [];

        foreach ($fields as $errorKey => $fieldName) {
            if (isset($errors[$errorKey])) {
                foreach ($errors[$errorKey] as $key => $message) {
                    $formMessages[$fieldsetName][$fieldName][] = $message;
                }

                unset($errors[$key]);
            }
        }

        $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');
        if (!empty($errors['application'])) {
            $fm->addCurrentErrorMessage($errors['application']);
        } elseif (!empty($errors)) {
            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
