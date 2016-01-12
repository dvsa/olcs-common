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
use Zend\Filter\Word\CamelCaseToDash;

/**
 * Licence History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
abstract class AbstractLicenceHistoryController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $sections = [
        'data' => [
            'prevHasLicence',
            'prevHadLicence',
            'prevBeenDisqualifiedTc',
        ],
        'eu' => [
            'prevBeenRefused',
            'prevBeenRevoked',
        ],
        'pi' => [
            'prevBeenAtPi',
        ],
        'assets' => [
            'prevPurchasedAssets'
        ]
    ];

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
    protected function getCrudAction(array $formTables = [])
    {
        $data = $formTables;

        $filter = new CamelCaseToDash();

        foreach ($this->sections as $group => $sections) {

            foreach ($sections as $section) {
                if (isset($data[$group][$section . '-table']['action'])) {

                    $action = $this->getActionFromCrudAction($data[$group][$section . '-table']);

                    $data[$group][$section . '-table']['routeAction'] = sprintf(
                        '%s-%s',
                        $filter->filter($section),
                        strtolower($action)
                    );

                    return $data[$group][$section . '-table'];
                }
            }
        }

        return null;
    }

    protected function delete()
    {
        $saveData = [
            'ids' => explode(',', $this->params('child_id'))
        ];

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->handleCommand(DeleteOtherLicence::create($saveData));
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

        $response = $this->handleCommand(UpdateLicenceHistory::create($data));

        if ($response->isOk()) {
            return true;
        }

        if ($response->isClientError()) {
            $this->mapErrorsForLicenceHistory($form, $response->getResult()['messages']);
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        return false;
    }

    protected function mapErrorsForLicenceHistory($form, array $errors)
    {
        $formMessages = [];

        foreach ($this->sections as $group => $sections) {
            foreach ($sections as $section) {
                if (isset($errors[$section])) {
                    foreach ($errors[$section] as $key => $message) {
                        $formMessages[$group][$section][] = $message;
                    }

                    unset($errors[$section]);
                }
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
        $saveData = [];

        foreach ($this->sections as $group => $sections) {
            foreach ($sections as $section) {
                if (isset($data[$group][$section])) {
                    $saveData[$section] = $data[$group][$section];
                }
            }
        }

        $saveData['version'] = $data['version'];

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
        return $this->handleQuery(LicenceHistory::create(['id' => $this->getIdentifier()]));
    }

    protected function formatDataForForm($data)
    {
        $data = $data['data'];
        $formData = [];

        foreach ($this->sections as $group => $sections) {
            foreach ($sections as $section) {
                $formData[$group][$section] = $data[$section];
            }
        }

        $formData['version'] = $data['version'];

        return $formData;
    }

    protected function getLicenceHistoryForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();

        foreach ($this->sections as $group => $sections) {
            foreach ($sections as $section) {
                $formHelper->populateFormTable(
                    $form->get($group)->get($section . '-table'),
                    $this->getTable($section),
                    $group . '[' . $section . '-table]'
                );
            }
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

        return $stringHelper->camelToUnderscore($section);
    }

    /**
     * Add prevHasLicence licence
     */
    public function prevHasLicenceAddAction()
    {
        return $this->addOrEdit('add', 'prevHasLicence');
    }

    /**
     * Edit prevHasLicence licence
     */
    public function prevHasLicenceEditAction()
    {
        return $this->addOrEdit('edit', 'prevHasLicence');
    }

    /**
     * Delete prevHasLicence licence
     */
    public function prevHasLicenceDeleteAction()
    {
        return $this->deleteAction(false);
    }

    /**
     * Add prevHadLicence licence
     */
    public function prevHadLicenceAddAction()
    {
        return $this->addOrEdit('add', 'prevHadLicence');
    }

    /**
     * Edit prevHadLicence licence
     */
    public function prevHadLicenceEditAction()
    {
        return $this->addOrEdit('edit', 'prevHadLicence');
    }

    /**
     * Delete prevHadLicence licence
     */
    public function prevHadLicenceDeleteAction()
    {
        return $this->deleteAction(false);
    }

    /**
     * Add prevBeenRefused licence
     */
    public function prevBeenRefusedAddAction()
    {
        return $this->addOrEdit('add', 'prevBeenRefused');
    }

    /**
     * Edit prevBeenRefused licence
     */
    public function prevBeenRefusedEditAction()
    {
        return $this->addOrEdit('edit', 'prevBeenRefused');
    }

    /**
     * Delete refused licence
     */
    public function prevBeenRefusedDeleteAction()
    {
        return $this->deleteAction(false);
    }

    /**
     * Add prevBeenRevoked licence
     */
    public function prevBeenRevokedAddAction()
    {
        return $this->addOrEdit('add', 'prevBeenRevoked');
    }

    /**
     * Edit prevBeenRevoked licence
     */
    public function prevBeenRevokedEditAction()
    {
        return $this->addOrEdit('edit', 'prevBeenRevoked');
    }

    /**
     * Delete prevBeenRevoked licence
     */
    public function prevBeenRevokedDeleteAction()
    {
        return $this->deleteAction(false);
    }

    /**
     * Add prevBeenDisqualifiedTc licence
     */
    public function prevBeenDisqualifiedTcAddAction()
    {
        return $this->addOrEdit('add', 'prevBeenDisqualifiedTc');
    }

    /**
     * Edit prevBeenDisqualifiedTc licence
     */
    public function prevBeenDisqualifiedTcEditAction()
    {
        return $this->addOrEdit('edit', 'prevBeenDisqualifiedTc');
    }

    /**
     * Delete prevBeenDisqualifiedTc licence
     */
    public function prevBeenDisqualifiedTcDeleteAction()
    {
        return $this->deleteAction(false);
    }

    /**
     * Add prevPurchasedAssets licence
     */
    public function prevPurchasedAssetsAddAction()
    {
        return $this->addOrEdit('add', 'prevPurchasedAssets');
    }

    /**
     * Edit prevPurchasedAssets licence
     */
    public function prevPurchasedAssetsEditAction()
    {
        return $this->addOrEdit('edit', 'prevPurchasedAssets');
    }

    /**
     * Delete prevPurchasedAssets licence
     */
    public function prevPurchasedAssetsDeleteAction()
    {
        return $this->deleteAction(false);
    }

    /**
     * Add prevBeenAtPi licence
     */
    public function prevBeenAtPiAddAction()
    {
        return $this->addOrEdit('add', 'prevBeenAtPi');
    }

    /**
     * Edit prevBeenAtPi licence
     */
    public function publicInquiryEditAction()
    {
        return $this->addOrEdit('edit', 'prevBeenAtPi');
    }

    /**
     * Delete prevBeenAtPi licence
     */
    public function prevBeenAtPiDeleteAction()
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

        $data = [];
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
        return $this->handleQuery(OtherLicence::create(['id' => $id]));
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

        if ($which !== 'prevBeenDisqualifiedTc') {
            $formHelper->remove($form, 'data->disqualificationDate');
            $formHelper->remove($form, 'data->disqualificationLength');
        }

        if ($which !== 'prevHasLicence') {
            $formHelper->remove($form, 'data->willSurrender');
        }

        if ($which !== 'prevPurchasedAssets') {
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

        return ['data' => $data];
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
            $dto = CreateOtherLicence::create($saveData);
        } else {
            $dto = UpdateOtherLicence::create($saveData);
        }

        $response = $this->handleCommand($dto);

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
