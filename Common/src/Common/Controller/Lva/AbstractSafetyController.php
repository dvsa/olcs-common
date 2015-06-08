<?php

/**
 * Safety Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Helper\FormHelperService;
use Common\Service\Entity\ContactDetailsEntityService;
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
     * @return array
     */
    abstract protected function getSafetyData();

    protected $canHaveTrailers;

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

        if ($request->isPost()) {

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

            $response = $this->handleQuery(Workshop::create(['id' => $id]));

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
                return $this->handlePostSave();
            }

            $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
        }

        return $this->render($mode . '_safety', $form);
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
        $form = $formHelper->createForm('Lva\Safety');
        $formHelper->populateFormTable(
            $form->get('table'),
            $this->getServiceLocator()->get('Table')->prepareTable('lva-safety', $this->workshops)
        );

        return $form;
    }
}
