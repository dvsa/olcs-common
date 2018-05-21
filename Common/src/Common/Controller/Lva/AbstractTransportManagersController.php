<?php

namespace Common\Controller\Lva;

use Common\Controller\Lva\Adapters\AbstractTransportManagerAdapter;
use Common\Controller\Lva\Interfaces\AdapterAwareInterface;
use Common\Controller\Lva\Interfaces\AdapterInterface;
use Common\Data\Mapper\Lva\NewTmUser as NewTmUserMapper;
use Common\Data\Mapper\Lva\TransportManagerApplication as TransportManagerApplicationMapper;
use Dvsa\Olcs\Transfer\Command;
use Dvsa\Olcs\Transfer\Query\User\UserSelfserve;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract Transport Managers Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTransportManagersController extends AbstractController implements
    AdapterAwareInterface,
    FactoryInterface
{
    use Traits\CrudTableTrait;

    protected $section = 'transport_managers';
    protected $lva = 'application';
    protected $location = 'external';

    /** @var  AbstractTransportManagerAdapter */
    protected $adapter;
    protected $baseRoute = 'lva-%s/transport_managers';

    /** @var  \Common\Service\Helper\FormHelperService */
    protected $hlpForm;
    /** @var  \Common\Service\Helper\TransportManagerHelperService */
    protected $hlpTransMngr;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->hlpForm = $serviceLocator->get('Helper\Form');
        $this->hlpTransMngr = $serviceLocator->get('Helper\TransportManager');

        return $this;
    }

    /**
     * Get Adapter
     *
     * @return AbstractTransportManagerAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set Adapter
     *
     * @param AdapterInterface $adapter Adapter
     *
     * @return void
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Transport Managers section
     *
     * @return array|\Common\View\Model\Section|\Zend\Http\Response
     */
    public function indexAction()
    {
        $this->getAdapter()->addMessages($this->getLicenceId());

        /** @var \Zend\Form\FormInterface $form */
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-transport_managers')
            ->getForm();

        $table = $this->getAdapter()->getTable('lva-transport-managers-'. $this->location .'-'. $this->lva);
        $tableData = $this->getAdapter()->getTableData($this->getIdentifier(), $this->getLicenceId());
        if ($tableData === null) {
            return $this->notFoundAction();
        }
        $table->loadData($tableData);
        $form->get('table')->get('table')->setTable($table);
        $form->get('table')->get('rows')->setValue(count($table->getRows()));

        $this->getServiceLocator()->get('FormServiceManager')
            ->get('Lva\\'. ucfirst($this->lva))
            ->alterForm($form);

        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->renderForm($form);
        }

        $data = (array)$request->getPost();
        $form->setData($data);

        //todo remove - this method may be removing the validator
        // if is it not required to have at least one TM, then remove the validator
        if (!$this->getAdapter()->mustHaveAtLeastOneTm()) {
            $form->getInputFilter()->remove('table');
        }

        $crudAction = $this->getCrudAction(array($data['table']));
        if ($crudAction !== null) {
            return $this->handleCrudAction($crudAction);
        }

        if ($form->isValid()) {
            if ($this->lva !== 'licence') {
                $data = ['id' => $this->getIdentifier(), 'section' => 'transportManagers'];
                $this->handleCommand(Command\Application\UpdateCompletion::create($data));
            }

            return $this->completeSection('transport_managers');
        }

        return $this->renderForm($form);
    }

    /**
     * Render Form
     *
     * @param \Zend\Form\FormInterface $form Form
     *
     * @return \Common\View\Model\Section
     */
    protected function renderForm($form)
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud-delta');

        return $this->render('transport_managers', $form);
    }

    /**
     * Process action - add
     *
     * @return \Common\View\Model\Section|\Zend\Http\Response
     */
    public function addAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        /** @var \Zend\Form\FormInterface $form */
        $form = $this->getAddForm();

        if ($request->isPost()) {
            $formData = (array)$request->getPost();

            if (isset($formData['data']['addUser'])) {
                return $this->redirect()->toRoute(null, ['action' => 'addNewUser'], [], true);
            }

            $formData = (array)$request->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                return $this->redirect()->toRoute(
                    null,
                    ['action' => 'addTm', 'child_id' => $formData['data']['registeredUser']],
                    [],
                    true
                );
            }
        }

        return $this->render('add-transport_managers', $form);
    }

    /**
     * Process Action - addTm
     *
     * @return \Common\View\Model\Section|\Zend\Http\Response
     */
    public function addTmAction()
    {
        $childId = $this->params('child_id');

        $user = $this->getCurrentUser();

        // User has selected [him/her]self
        // So we don't need to continue to show the form
        if ($user['id'] == $childId) {
            $form = $this->getAddForm();

            $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')
                ->createCommand(
                    Command\TransportManagerApplication\Create::create(
                        ['application' => $this->getIdentifier(), 'user' => $childId, 'action' => 'A']
                    )
                );
            /* @var $response \Common\Service\Cqrs\Response */
            $response = $this->getServiceLocator()->get('CommandService')->send($command);

            if ($response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            if ($response->isClientError()) {
                $errors = TransportManagerApplicationMapper::mapFromErrors($form, $response->getResult());

                foreach ($errors as $error) {
                    $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($error);
                }
            }

            if ($response->isOk()) {
                return $this->redirect()->toRouteAjax(
                    null,
                    [
                        'action' => 'details',
                        'child_id' => $response->getResult()['id']['transportManagerApplication']
                    ],
                    [],
                    true
                );
            }
            return $this->redirect()->toRoute(
                null,
                [
                    'action' => 'add',
                    'child_id' => null,
                ],
                [],
                true
            );
        }

        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(UserSelfserve::create(['id' => $childId]));

        /* @var $response \Common\Service\Cqrs\Response */
        $response = $this->getServiceLocator()->get('QueryService')->send($query);
        $userDetails = $response->getResult();

        /** @var \Zend\Form\FormInterface $form */
        $form = $this->getTmDetailsForm($userDetails['contactDetails']['emailAddress']);
        $formData = [
            'data' => [
                'forename' => $userDetails['contactDetails']['person']['forename'],
                'familyName' => $userDetails['contactDetails']['person']['familyName'],
                'email' => $userDetails['contactDetails']['emailAddress'],
                'birthDate' => $userDetails['contactDetails']['person']['birthDate'],
            ]
        ];

        if ($request->isPost()) {
            $postData = (array)$request->getPost();
            unset($formData['data']['birthDate']);
            $formData = array_merge_recursive($postData, $formData);
        }

        $form->setData($formData);

        if ($request->isPost() && $form->isValid()) {
            $formData = $form->getData();

            // create TMA
            $command = Command\TransportManagerApplication\Create::create(
                [
                    'application' => $this->getIdentifier(),
                    'user' => $childId,
                    'action' => 'A',
                    'dob' => $formData['data']['birthDate'],
                ]
            );
            /* @var $response \Common\Service\Cqrs\Response */
            $response = $this->handleCommand($command);

            if ($response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            if ($response->isClientError()) {
                $errors = TransportManagerApplicationMapper::mapFromErrors($form, $response->getResult());

                foreach ($errors as $error) {
                    $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($error);
                }
            }

            if ($response->isOk()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addSuccessMessage('lva-tm-sent-success');

                return $this->redirect()->toRouteAjax(
                    $this->getBaseRoute(),
                    [
                        'action' => null
                    ],
                    [],
                    true
                );
            }
        }

        return $this->render('addTm-transport_managers', $form);
    }

    /**
     * Process action - addNewUser
     *
     * @return \Common\View\Model\Section|\Zend\Http\Response
     */
    public function addNewUserAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        /** @var \Common\Form\Form $form */
        $form = $formHelper->createFormWithRequest('Lva\NewTmUser', $request);

        if ($request->isPost()) {
            $form->setData((array)$request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $hasEmail = isset($data['data']['hasEmail']) ? $data['data']['hasEmail'] : null;

                $command = Command\Tm\CreateNewUser::create(
                    [
                        'application' => $this->getIdentifier(),
                        'firstName' => $data['data']['forename'],
                        'familyName' => $data['data']['familyName'],
                        'birthDate' => $data['data']['birthDate'],
                        'hasEmail' => $hasEmail,
                        'username' => $data['data']['username'],
                        'emailAddress' => $data['data']['emailAddress'],
                        'translateToWelsh' => $data['data']['translateToWelsh'],
                    ]
                );

                $response = $this->handleCommand($command);

                $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

                if ($response->isOk()) {
                    if ($hasEmail === 'Y') {
                        $successMessage = 'tm-add-user-success-message';
                    } else {
                        $successMessage = 'tm-add-user-success-message-no-email';
                    }

                    $fm->addSuccessMessage($successMessage);

                    return $this->redirect()->toRouteAjax($this->getBaseRoute(), ['action' => null], [], true);
                }

                if ($response->isServerError()) {
                    $fm->addCurrentUnknownError();
                } else {
                    $messages = $response->getResult()['messages'];

                    NewTmUserMapper::mapFormErrors($form, $messages, $fm);
                }
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-tm-add-user');

        return $this->render('add-transport_managers', $form);
    }

    /**
     * Get Transport Manager Details Form
     *
     * @param string $email E-mail
     *
     * @return \Zend\Form\FormInterface
     */
    protected function getTmDetailsForm($email)
    {
        /** @var \Zend\Form\FormInterface $form */
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\AddTransportManagerDetails', $this->getRequest());

        $form->get('data')->get('guidance')->setTokens([$email]);

        return $form;
    }

    /**
     * Get Add Form
     *
     * @return \Zend\Form\FormInterface
     */
    protected function getAddForm()
    {
        /** @var \Zend\Form\FormInterface $form */
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\AddTransportManager', $this->getRequest());

        $orgId = $this->getCurrentOrganisationId();

        $registeredUsers = $this->getOrganisationUsersForSelect($orgId);

        $form->get('data')->get('registeredUser')->setEmptyOption('Please select');
        $form->get('data')->get('registeredUser')->setValueOptions($registeredUsers);

        return $form;
    }

    /**
     * Get users in organisation for use in a select element
     *
     * @param int $organisationId Organisation Id
     *
     * @return array
     */
    protected function getOrganisationUsersForSelect($organisationId)
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(\Dvsa\Olcs\Transfer\Query\User\UserList::create(['organisation' => $organisationId]));
        /* @var $response \Common\Service\Cqrs\Response */
        $response = $this->getServiceLocator()->get('QueryService')->send($query);
        $options = [];
        foreach ($response->getResult()['results'] as $user) {
            $name = $user['contactDetails']['person']['forename'] .' '. $user['contactDetails']['person']['familyName'];
            if (empty(trim($name))) {
                $name = 'User ID '. $user['id'];
            }
            $options[$user['id']] = $name;
        }
        asort($options);

        return $options;
    }

    /**
     * Handle CrudTableTrait delete
     *
     * @param $optOut licence.opt_out_tm_letter flag
     *
     * @return void
     */
    protected function delete($optOut = null)
    {
        // get ids to delete
        //$ids = explode(',', $this->params('child_id'));
        // @todo uncomment 
        $ids = [1];

        $this->getAdapter()->delete($ids, $this->getIdentifier(), $optOut, $this->isLastTmLicence());
    }

    /**
     * Override the delete title.
     *
     * @return string The modal message key.
     */
    protected function getDeleteTitle()
    {
        return 'delete-tm';
    }

    /**
     * Override the delete title.
     *
     * @return string The modal message key.
     */
    protected function getDeleteMessage()
    {
        if ($this->isLastTmLicence()) {
            return 'delete.final-tm.confirmation.text';
        }

        return 'delete.confirmation.text';
    }

    /**
     * Checks if number of Tm's on licence = 1
     *
     * @return bool
     */
    protected function isLastTmLicence() {
        if ($this->lva === 'licence'
            && $this->getAdapter()->getNumberOfRows($this->getIdentifier(), $this->getLicenceId()) === 1) {
            return true;
        }

        return false;
    }

    /**
     * Restore Transport Managers
     *
     * @return \Zend\Http\Response
     */
    public function restoreAction()
    {
        $ids = explode(',', $this->params('child_id'));

        // get table data
        $data = $this->getAdapter()->getTableData($this->getIdentifier(), $this->getLicenceId());

        $tmaIdsToDelete = [];
        foreach ($ids as $id) {
            if (strpos($id, 'L') === 0) {
                $tmaId = $this->findTmaId($data, $id);
                $tmaIdsToDelete[] = $tmaId;
            } else {
                // add TMA ID to delete array
                $tmaIdsToDelete[] = $id;
            }
        }

        if (!empty($tmaIdsToDelete)) {
            $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')
                ->createCommand(
                    Command\TransportManagerApplication\Delete::create(
                        ['ids' => array_unique($tmaIdsToDelete)]
                    )
                );
            $this->getServiceLocator()->get('CommandService')->send($command);
        }

        return $this->redirect()->toRouteAjax($this->getBaseRoute(), [], [], true);
    }

    /**
     * Find the Transport Manager application ID that is linked to Transport Manager application ID
     *
     * @param array  $data  Data
     * @param string $tmlId This is the TML ID prefixed with an "L"
     *
     * @return int|false The TMA ID or false if not found
     */
    protected function findTmaId($data, $tmlId)
    {
        foreach ($data as $tmId => $row) {
            if ($row['id'] === $tmlId) {
                return $data[$tmId .'a']['id'];
            }
        }

        return false;
    }
}
