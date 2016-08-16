<?php

/**
 * Safety Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Helper\FormHelperService;
use Dvsa\Olcs\Transfer\Command\Workshop\CreateWorkshop as LicenceCreateWorkshop;
use Dvsa\Olcs\Transfer\Command\Application\CreateWorkshop as ApplicationCreateWorkshop;
use Dvsa\Olcs\Transfer\Command\Workshop\UpdateWorkshop as LicenceUpdateWorkshop;
use Dvsa\Olcs\Transfer\Command\Application\UpdateWorkshop as ApplicationUpdateWorkshop;
use Dvsa\Olcs\Transfer\Query\Workshop\Workshop;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 * Safety Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractSafetyController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $section = 'safety';

    /**
     * Shared action data map
     *
     * @var array
     */
    protected $safetyProvidersActionDataMap = array(
        '_addresses' => array(
            'address'
        ),
        'main' => array(
            'children' => array(
                'workshop' => array(
                    'mapFrom' => array(
                        'data'
                    )
                ),
                'contactDetails' => array(
                    'mapFrom' => array(
                        'contactDetails'
                    ),
                    'children' => array(
                        'addresses' => array(
                            'mapFrom' => array(
                                'addresses'
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Save the form data
     *
     * @param array $data
     */
    abstract protected function save($data, $partial);

    /**
     * Get Safety Data
     *
     * @param bool $noCache
     * @return array
     */
    abstract protected function getSafetyData($noCache = false);

    protected $canHaveTrailers;
    protected $showTrailers;

    protected $workshops;

    protected $createWorkshopCommandMap = [
        'licence' => LicenceCreateWorkshop::class,
        'application' => ApplicationCreateWorkshop::class,
        'variation' => ApplicationCreateWorkshop::class,
    ];

    protected $updateWorkshopCommandMap = [
        'licence' => LicenceUpdateWorkshop::class,
        'application' => ApplicationUpdateWorkshop::class,
        'variation' => ApplicationUpdateWorkshop::class,
    ];

    protected $safetyData = null;

    /**
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        // We always want to get the result
        $result = $this->getSafetyData();
        if ($result instanceof ViewModel) {
            return $result;
        }

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($result);
        }

        $form = $this->alterForm($this->getSafetyForm())->setData($data);

        $hasProcessedFiles = $this->processFiles(
            $form,
            'additional-documents->files',
            array($this, 'processSafetyAdditionalDocumentsFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments')
        );

        if (!$hasProcessedFiles && $request->isPost()) {

            $crudAction = $this->getCrudAction([$data['table']]);

            $partial = false;

            if ($crudAction !== null) {
                $partial = true;
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {

                $response = $this->save($data, $partial);

                if ($response->isOk()) {
                    if ($crudAction !== null) {
                        return $this->handleCrudAction($crudAction);
                    }

                    return $this->completeSection('safety');
                }

                if ($response->isServerError()) {
                    $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
                } else {
                    $this->mapErrors($form, $response->getResult()['messages']);
                }
            }
        }

        $this->getServiceLocator()->get('Script')->loadFiles(['vehicle-safety', 'lva-crud']);

        return $this->render('safety', $form);
    }

    /**
     * Callback to handle the file upload
     *
     * @param array $file
     */
    public function processSafetyAdditionalDocumentsFileUpload($file)
    {
        $id = $this->getIdentifier();

        $data = array_merge(
            $this->getUploadMetaData($file, $id),
            [
                'isExternal' => $this->isExternal()
            ]
        );

        $this->uploadFile($file, $data);

        $this->getSafetyData(true);
    }

    /**
     * Callback to get list of documents
     *
     * @return array
     */
    public function getDocuments()
    {
        $data = $this->getSafetyData();
        return isset($data['safetyDocuments']) ? $data['safetyDocuments'] : [];
    }

    protected function mapErrors(Form $form, array $errors)
    {
        $formMessages = [];

        if (isset($errors['safetyConfirmation'])) {

            foreach ($errors['safetyConfirmation'][0] as $key => $message) {
                $formMessages['application']['safetyConfirmation'][] = $key;
            }

            unset($errors['safetyConfirmation']);
        }

        if (isset($errors['tachographInsName'])) {

            foreach ($errors['tachographInsName'][0] as $key => $message) {
                $formMessages['licence']['tachographInsName'][] = $key;
            }

            unset($errors['tachographInsName']);
        }

        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
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
     * Delete
     */
    protected function delete()
    {
        $ids = explode(',', $this->params('child_id'));

        $response = $this->deleteWorkshops($ids);

        if (!$response->isOk()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }
    }

    /**
     * Helper method as both add and edit pretty
     * much do the same thing
     *
     * @param string $mode
     */
    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $safetyProviderData = array();
        $data = array();
        $id = $this->params('child_id');

        if ($mode !== 'add') {
            $dtoParams = [
                $this->getIdentifierIndex() => $this->getIdentifier(),
                'id' => $id
            ];
            $response = $this->handleQuery(Workshop::create($dtoParams));

            if (!$response->isOK()) {
                return $this->notFoundAction();
            }

            $safetyProviderData = $response->getResult();
        }

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->formatCrudDataForForm($safetyProviderData, $mode);
        }

        $form = $this->getSafetyProviderForm()->setData($data);
        $this->alterExternalHint($form);

        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        $addressLookup = $this->getServiceLocator()->get('Helper\Form')->processAddressLookupForm($form, $request);

        if (!$addressLookup && $request->isPost() && $form->isValid()) {

            $dtoData = [
                $this->getIdentifierIndex() => $this->getIdentifier(),
                'isExternal' => $data['data']['isExternal'],
                'contactDetails' => $data['contactDetails']
            ];

            $dtoData['contactDetails']['address'] = $data['address'];

            if ($mode === 'add') {
                $command = $this->createWorkshopCommandMap[$this->lva];
            } else {
                $dtoData['id'] = $id;
                $dtoData['version'] = $data['data']['version'];
                $command = $this->updateWorkshopCommandMap[$this->lva];
            }

            $dto = $command::create($dtoData);

            $response = $this->handleCommand($dto);

            if ($response->isOk()) {
                return $this->handlePostSave(null, false);
            }

            $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
        }

        $this->getServiceLocator()->get('Script')->loadFiles(['safety-inspector-add']);

        return $this->render($mode . '_safety', $form);
    }

    /**
     * Alter the hint for the isExternal form element
     *
     * @param Form $form The add/edit form
     *
     * @return void
     */
    protected function alterExternalHint(Form $form)
    {
        $data = null;
        if ($this->lva === 'licence') {
            // load licence data
            $dto = \Dvsa\Olcs\Transfer\Query\Licence\Licence::create(['id' => $this->getIdentifier()]);
        } else {
            // load application/variation data
            $dto = \Dvsa\Olcs\Transfer\Query\Application\Application::create(['id' => $this->getIdentifier()]);
        }
        // load application/variation data
        $response = $this->handleQuery($dto);
        if ($response->isOk()) {
            $data = $response->getResult();
            $ref = $data['niFlag'] . '-' . $data['goodsOrPsv']['id'];
            $hints = [
                'N-' . \Common\RefData::LICENCE_CATEGORY_GOODS_VEHICLE => 'safety-inspector-external-hint-GB-goods',
                'N-' . \Common\RefData::LICENCE_CATEGORY_PSV => 'safety-inspector-external-hint-GB-psv',
                'Y-' . \Common\RefData::LICENCE_CATEGORY_GOODS_VEHICLE => 'safety-inspector-external-hint-NI-goods',
            ];

            // Add a hint to the external radio
            /** @var \Zend\Form\Element\Radio $ee */
            $externalElement = $form->get('data')->get('isExternal');
            $externalElement->setOption('hint', $hints[$ref]);
        }
    }

    /**
     * Format data for the safety providers table
     *
     * @param array $data
     * @param string $mode
     * @return array
     */
    protected function formatCrudDataForForm($data, $mode)
    {
        if ($mode == 'edit') {
            $data['data'] = array(
                'version' => $data['version'],
                'isExternal' => $data['isExternal']
            );

            $data['address'] = $data['contactDetails']['address'];
            $data['address']['countryCode'] = $data['address']['countryCode']['id'];

            unset($data['version']);
            unset($data['isExternal']);
            unset($data['contactDetails']['address']);
        }

        return $data;
    }

    /**
     * Get safety provider form
     *
     * @return \Zend\Form\Form
     */
    protected function getSafetyProviderForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\SafetyProviders', $this->getRequest());
    }

    /**
     * Alter form
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterForm($form)
    {
        /** @var FormHelperService $formHelper */
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        // This element needs to be visible internally
        $formHelper->remove($form, 'application->isMaintenanceSuitable');

        if (!$this->canHaveTrailers) {

            $formHelper->remove($form, 'licence->safetyInsTrailers');

            $formHelper->alterElementLabel(
                $form->get('licence')->get('safetyInsVaries'),
                '.psv',
                FormHelperService::ALTER_LABEL_APPEND
            );

            $table = $form->get('table')->get('table')->getTable();

            $emptyMessage = $table->getVariable('empty_message');
            $table->setVariable('empty_message', $emptyMessage . '-psv');

            $form->get('table')->get('table')->setTable($table);
        } elseif (!$this->showTrailers) {
            $formHelper->remove($form, 'licence->safetyInsTrailers');
        }

        $this->alterFormForLva($form);

        return $form;
    }

    /**
     * Format data for form
     *
     * @param array $data
     */
    protected function formatDataForForm($data)
    {
        if (isset($data['licence']['tachographIns']['id'])) {
            $data['licence']['tachographIns'] = $data['licence']['tachographIns']['id'];
        }

        $data['application'] = array(
            'version' => $data['version'],
            'safetyConfirmation' => $data['safetyConfirmation'],
            'isMaintenanceSuitable' => $data['isMaintenanceSuitable']
        );

        unset($data['version']);
        unset($data['safetyConfirmation']);
        unset($data['isMaintenanceSuitable']);

        return $data;
    }

    /**
     * Get safety form
     *
     * @return \Zend\Form\Form
     */
    protected function getSafetyForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        /** @var \Zend\Form\Form $form */
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();

        $formHelper->populateFormTable(
            $form->get('table'),
            $this->getServiceLocator()->get('Table')->prepareTable('lva-safety', $this->workshops)
        );

        return $form;
    }
}
