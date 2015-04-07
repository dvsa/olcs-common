<?php

/**
 * Abstract Transport Managers Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;

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

    /**
     * Transport managers section
     */
    public function indexAction()
    {
        /* @var $form \Zend\Form\Form */
        $form = $this->getAdapter()->getForm();
        $table = $this->getAdapter()->getTable();
        $table->loadData($this->getAdapter()->getTableData($this->getIdentifier()));
        $form->get('table')->get('table')->setTable($table);
        $form->get('table')->get('rows')->setValue(count($table->getRows()));

        $request = $this->getRequest();
        if ($request->isPost()) {

            $data = (array) $request->getPost();
            $form->setData($data);

            // if is it not required to have at least one TM, then remove the validator
            if (!$this->getAdapter()->mustHaveAtLeastOneTm($this->getIdentifier())) {
                $form->getInputFilter()->remove('table');
            }

            $crudAction = $this->getCrudAction(array($data['table']));
            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }

            if ($form->isValid()) {
                $this->postSave('transport_managers');
                return $this->completeSection('transport_managers');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

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

            $params = [
                'userId' => $user['id'],
                'applicationId' => $this->getIdentifier()
            ];

            $response = $this->getServiceLocator()->get('BusinessServiceManager')
                // Should be fine to hard code Application here, as this page is only accessible for
                // new apps and variations
                ->get('Lva\TransportManagerApplication')
                ->process($params);

            return $this->redirect()->toRouteAjax(
                null,
                [
                    'action' => 'details',
                    'child_id' => $response->getData()['linkId']
                ],
                [],
                true
            );
        }

        $request = $this->getRequest();

        $userDetails = $this->getServiceLocator()->get('Entity\User')->getUserDetails($childId);

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

            $params = [
                'userId' => $user['id'],
                'applicationId' => $this->getIdentifier(),
                'dob' => $formData['data']['birthDate']
            ];

            $response = $this->getServiceLocator()->get('BusinessServiceManager')
                ->get('Lva\SendTransportManagerApplication')
                ->process($params);

            return $this->redirect()->toRouteAjax(
                null,
                [
                    'action' => 'details',
                    'child_id' => $response->getData()['linkId']
                ],
                [],
                true
            );
        }

        return $this->render('addTm-transport_managers', $form);
    }

    protected function getTmDetailsForm($email)
    {
        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\AddTransportManagerDetails');

        $form->get('data')->get('guidance')->setTokens([$email]);

        return $form;
    }

    protected function getAddForm()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\AddTransportManager');

        $orgId = $this->getCurrentOrganisationId();

        $registeredUsers = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getRegisteredUsersForSelect($orgId);

        $form->get('data')->get('registeredUser')->setEmptyOption('Please select');
        $form->get('data')->get('registeredUser')->setValueOptions($registeredUsers);

        return $form;
    }

    /**
     * Handle CrudTableTrait delete
     */
    protected function delete()
    {
        // get ids to delete
        $ids = explode(',', $this->params('child_id'));

        /* @var $service \Common\BusinessService\Service\TransportManagerApplication\Delete */
        $service = $this->getServiceLocator()
            ->get('BusinessServiceManager')
            ->get('Lva\DeleteTransportManagerApplication');
        $service->process(['ids' => $ids]);
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
}
