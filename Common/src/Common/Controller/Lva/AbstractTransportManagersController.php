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
    protected $lva = 'application';
    protected $location = 'external';

    /**
     * Transport managers section
     */
    public function indexAction()
    {
        /* @var $form \Zend\Form\Form */
        $form = $this->getAdapter()->getForm();
        $table = $this->getAdapter()->getTable('lva-transport-managers-'. $this->location .'-'. $this->lva);
        $table->loadData($this->getAdapter()->getTableData($this->getIdentifier(), $this->getLicenceId()));
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

            $params = [
                'userId' => $childId,
                'applicationId' => $this->getIdentifier()
            ];

            $response = $this->getServiceLocator()->get('BusinessServiceManager')
                // Should be fine to hard code Application here, as this page is only accessible for
                // new apps and variations
                ->get('Lva\TransportManagerApplicationForUser')
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
                'userId' => $childId,
                'applicationId' => $this->getIdentifier(),
                'dob' => $formData['data']['birthDate']
            ];

            $this->getServiceLocator()->get('BusinessServiceManager')
                ->get('Lva\SendTransportManagerApplication')
                ->process($params);

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

        $tmaIdsToDelete = [];
        foreach ($ids as $id) {
            if (strpos($id, 'L') === 0) {
                // remove "L" prefix and get int ID
                $transportManagerLicenceId = (int) trim($id, 'L');

                // get the transport manager ID from TML
                $tmlEntityService = $this->getServiceLocator()->get('Entity\TransportManagerLicence');
                $tmlData = $tmlEntityService->getTransportManagerLicence($transportManagerLicenceId);
                $transportManagerId = $tmlData['transportManager']['id'];

                // get the transport manager application ID using TM
                $tmaEntityService = $this->getServiceLocator()->get('Entity\TransportManagerApplication');
                $tmaData = $tmaEntityService->getByApplicationTransportManager(
                    $this->getIdentifier(),
                    $transportManagerId
                );
                foreach ($tmaData['Results'] as $row) {
                    $tmaIdsToDelete[] = $row['id'];
                }
            } else {
                // add TMA ID to delete array
                $tmaIdsToDelete[] = $id;
            }
        }

        // remove any duplicates, eg if restoring the current and updated versions
        $tmaIdsToDelete = array_unique($tmaIdsToDelete);

        // if any TMA ID added to array then delete them
        if (count($tmaIdsToDelete) > 0) {
            $tmaDeleteService = $this->getServiceLocator()
                ->get('BusinessServiceManager')
                    ->get('Lva\DeleteTransportManagerApplication');
            $tmaDeleteService->process(['ids' => $tmaIdsToDelete]);
        }

        return $this->redirect()->toRouteAjax(null, ['action' => null], [], true);
    }
}
