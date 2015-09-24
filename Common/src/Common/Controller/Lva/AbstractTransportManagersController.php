<?php

/**
 * Abstract Transport Managers Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;
use Dvsa\Olcs\Transfer\Command;
use Common\Data\Mapper\Lva\TransportManagerApplication as TransportManagerApplicationMapper;

/**
 * Abstract Transport Managers Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTransportManagersController extends AbstractController implements AdapterAwareInterface
{
    use Traits\CrudTableTrait,
        Traits\AdapterAwareTrait;

    protected $section = 'transport_managers';
    protected $lva = 'application';
    protected $location = 'external';

    /**
     * Transport managers section
     */
    public function indexAction()
    {
        $this->getAdapter()->addMessages($this->getLicenceId());

        /* @var $form \Zend\Form\Form */
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-transport_managers')
            ->getForm();

        $table = $this->getAdapter()->getTable('lva-transport-managers-'. $this->location .'-'. $this->lva);
        $table->loadData($this->getAdapter()->getTableData($this->getIdentifier(), $this->getLicenceId()));
        $form->get('table')->get('table')->setTable($table);
        $form->get('table')->get('rows')->setValue(count($table->getRows()));

        $this->getServiceLocator()->get('FormServiceManager')
            ->get('Lva\\'. ucfirst($this->lva))
            ->alterForm($form);

        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->renderForm($form);
        }

        $data = (array) $request->getPost();
        $form->setData($data);

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

    protected function renderForm($form)
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud-delta');

        return $this->render('transport_managers', $form);
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form = $this->getAddForm();

        if ($request->isPost()) {
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
                ['action' => 'add'],
                [],
                true
            );
        }

        $request = $this->getRequest();

        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(\Dvsa\Olcs\Transfer\Query\User\User::create(['id' => $childId]));
        /* @var $response \Common\Service\Cqrs\Response */
        $response = $this->getServiceLocator()->get('QueryService')->send($query);
        $userDetails = $response->getResult();

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

            // Update DOB
            $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')
                ->createCommand(
                    Command\Person\Update::create(
                        [
                            'id' => $userDetails['contactDetails']['person']['id'],
                            'dob' => $formData['data']['birthDate']
                        ]
                    )
                );
            /* @var $response \Common\Service\Cqrs\Response */
            $response = $this->getServiceLocator()->get('CommandService')->send($command);

            // create TMA
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
                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addSuccessMessage('lva-tm-sent-success');

                return $this->redirect()->toRouteAjax(
                    null,
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

    protected function getTmDetailsForm($email)
    {
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\AddTransportManagerDetails', $this->getRequest());

        $form->get('data')->get('guidance')->setTokens([$email]);

        return $form;
    }

    protected function getAddForm()
    {
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
     * @param int $organisationId
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
     */
    protected function delete()
    {
        // get ids to delete
        $ids = explode(',', $this->params('child_id'));

        $this->getAdapter()->delete($ids, $this->getIdentifier());
    }

    /**
     * Gives a new translation key to use for the delete modal text.
     *
     * @return string The message translation key.
     */
    protected function getDeleteMessage()
    {
        return 'review-transport_managers_delete';
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
     * Restore Transport managers
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

        return $this->redirect()->toRouteAjax(null, ['action' => null], [], true);
    }

    /**
     * Find the Transport manager application ID that is linked to Transport manager application ID
     *
     * @param array  $data
     * @param string $tmlId This is the TML ID prefixed with an "L"
     * @param int    $applicationId
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
