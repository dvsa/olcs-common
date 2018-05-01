<?php

namespace Common\Controller\Lva;

use Common\Service\Helper\FormHelperService;
use Dvsa\Olcs\Transfer\Command\Application\CreateWorkshop as ApplicationCreateWorkshop;
use Dvsa\Olcs\Transfer\Command\Application\UpdateWorkshop as ApplicationUpdateWorkshop;
use Dvsa\Olcs\Transfer\Command\Workshop\CreateWorkshop as LicenceCreateWorkshop;
use Dvsa\Olcs\Transfer\Command\Workshop\UpdateWorkshop as LicenceUpdateWorkshop;
use Dvsa\Olcs\Transfer\Query\Workshop\Workshop;
use Zend\Form\Form;
use Zend\Form\FormInterface;
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

    const DEFAULT_TABLE_RECORDS_COUNT = 10;

    protected $section = 'safety';
    protected $baseRoute = 'lva-%s/safety';

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

    protected $canHaveTrailers;
    protected $isShowTrailers;

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
     * Delete Workshops
     *
     * @param array $ids Identifiers
     *
     * @return \Common\Service\Cqrs\Response
     */
    abstract protected function deleteWorkshops($ids);

    /**
     * Save the form data
     *
     * @param array $data    Form Data
     * @param bool  $partial Is partial post
     *
     * @return \Common\Service\Cqrs\Response
     */
    abstract protected function save($data, $partial);

    /**
     * Get Safety Data
     *
     * @param bool $noCache No Cache
     *
     * @return array
     */
    abstract protected function getSafetyData($noCache = false);

    /**
     * Redirect to the first section
     *
     * @return array|\Common\View\Model\Section|Response
     */
    public function indexAction()
    {
        /** @var \Zend\Http\Request $request */
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

        if ($request->isPost()) {
            $crudAction = $this->getCrudAction([$data['table']]);
            $haveCrudAction = ($crudAction !== null);

            if ($haveCrudAction) {
                if ($this->isInternalReadOnly()) {
                    return $this->handleCrudAction($crudAction);
                }

                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {
                $response = $this->save($data, $haveCrudAction);

                if ($response->isOk()) {
                    if ($haveCrudAction) {
                        return $this->handleCrudAction($crudAction);
                    }

                    return $this->completeSection('safety');
                }

                if ($response->isServerError()) {
                    $this->getServiceLocator()->get('Helper\FlashMessenger')->addUnknownError();
                } else {
                    $this->mapErrors($form, $response->getResult()['messages']);
                }
            }
        }

        $this->getServiceLocator()->get('Script')->loadFiles(['vehicle-safety', 'lva-crud']);

        return $this->render('safety', $form);
    }

    /**
     * Map Errors
     *
     * @param FormInterface $form   Form
     * @param array         $errors Errors
     *
     * @return void
     */
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
     *
     * @return \Common\View\Model\Section|Response
     */
    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    /**
     * Edit person action
     *
     * @return \Common\View\Model\Section|Response
     */
    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * Delete
     *
     * @return void
     */
    protected function delete()
    {
        $ids = explode(',', $this->params('child_id'));

        $response = $this->deleteWorkshops($ids);

        if (!$response->isOk()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addUnknownError();
        }
    }

    /**
     * Get delete modal title
     *
     * @return string
     */
    protected function getDeleteTitle()
    {
        return 'delete-safety-inspector';
    }

    /**
     * Helper method as both add and edit pretty
     * much do the same thing
     *
     * @param string $mode Mode
     *
     * @return \Common\View\Model\Section|Response
     */
    protected function addOrEdit($mode)
    {
        /** @var \Zend\Http\Request $request */
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

            if (!$response->isOk()) {
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
                return $this->handlePostSave(null, ['fragment' => 'table']);
            }

            $this->getServiceLocator()->get('Helper\FlashMessenger')->addUnknownError();
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
            $translationHelper = $this->getServiceLocator()->get('Helper\Translation');
            $data = $response->getResult();

            $ref = $data['niFlag'] . '-' . $data['goodsOrPsv']['id'];
            $links = [
                'N-' . \Common\RefData::LICENCE_CATEGORY_GOODS_VEHICLE => 'safety-inspector-sample-contract-GV79-GB',
                'N-' . \Common\RefData::LICENCE_CATEGORY_PSV => 'safety-inspector-sample-contract-PSV421-GB',
                'Y-' . \Common\RefData::LICENCE_CATEGORY_GOODS_VEHICLE => 'safety-inspector-sample-contract-GV79-NI',
            ];

            $hint = $translationHelper->translateReplace(
                'safety-inspector-external-hint',
                [$this->url()->fromRoute(
                    'getfile',
                    ['identifier' => base64_encode(
                        $translationHelper->translate($links[$ref])
                    )]
                )]
            );

            // Add a hint to the external radio
            /** @var \Zend\Form\Element\Radio $externalElement */
            $externalElement = $form->get('data')->get('isExternal');
            $externalElement->setOption('hint', $hint);
        }
    }

    /**
     * Format data for the safety providers table
     *
     * @param array  $data Data
     * @param string $mode Mode
     *
     * @return array
     */
    protected function formatCrudDataForForm($data, $mode)
    {
        if ($mode === 'edit') {
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
     * @param \Zend\Form\FormInterface $form Form
     *
     * @return \Zend\Form\FormInterface
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

            $form->get('table')->get('table')->setTable($table);
        } elseif (!$this->isShowTrailers) {
            $formHelper->remove($form, 'licence->safetyInsTrailers');
        }

        $this->alterFormForLva($form);

        return $form;
    }

    /**
     * Format data for form
     *
     * @param array $data Data
     *
     * @return array
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
     * @return \Zend\Form\FormInterface
     */
    protected function getSafetyForm()
    {
        /** @var \Common\Service\Table\TableBuilder $table */
        $table = $this->getServiceLocator()
            ->get('Table')
            ->prepareTable('lva-safety', $this->workshops, (array) $this->getRequest()->getQuery());

        if ($this->location === 'external') {
            $table->removeColumn('isExternal');
        }

        /** @var \Zend\Form\Form $form */
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();

        $this->getServiceLocator()->get('Helper\Form')
            ->populateFormTable($form->get('table'), $table);

        return $form;
    }
}
