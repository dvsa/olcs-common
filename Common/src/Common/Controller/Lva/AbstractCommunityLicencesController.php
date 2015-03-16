<?php

/**
 * Shared logic between Community Licences controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\CommunityLicEntityService;
use Common\Controller\Lva\Interfaces\AdapterAwareInterface;
use Zend\View\Model\ViewModel;

/**
 * Shared logic between Community Licences controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractCommunityLicencesController extends AbstractController implements AdapterAwareInterface
{
    use Traits\CrudTableTrait,
        Traits\AdapterAwareTrait;

    protected $section = 'community_licences';

    protected $defaultFilters = [
        'status' => [
            CommunityLicEntityService::STATUS_PENDING,
            CommunityLicEntityService::STATUS_ACTIVE,
            CommunityLicEntityService::STATUS_WITHDRAWN,
            CommunityLicEntityService::STATUS_SUSPENDED
        ]
    ];

    protected $filters = [];

    /**
     * Community Licences section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $crudAction = $this->getCrudAction([$data['table']]);
            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction, ['add', 'office-licence-add']);
            }
            $this->postSave('community_licences');
            return $this->completeSection('community_licences');
        }

        $filterStatuses = $this->params()->fromQuery('status');
        $hasFiltered = $this->params()->fromQuery('isFiltered');

        if (empty($filterStatuses) && empty($hasFiltered)) {
            $this->filters = $this->defaultFilters;
        } else {
            $this->filters = [
                'status' => empty($filterStatuses) ? 'NULL': $filterStatuses
            ];
        }

        $filterForm = $this->getFilterForm()->setData($this->filters);

        $form = $this->getForm();

        $this->alterFormForLva($form);
        $this->alterFormForLocation($form);

        $data = $this->formatDataForForm($this->getFormData());
        $form->setData($data);

        $this->getServiceLocator()->get('Script')->loadFiles(['forms/filter', 'community-licence']);

        return $this->render('community_licences', $form, ['filterForm' => $filterForm]);
    }

    /**
     * Get filter form
     *
     * @return \Zend\Form\Form
     */
    private function getFilterForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\CommunityLicenceFilter', false);
    }

    /**
     * Get form
     *
     * @return \Zend\Form\Form
     */
    private function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\CommunityLicences');

        $table = $this->alterTable($this->getTable());
        $formHelper->populateFormTable($form->get('table'), $table);

        return $form;
    }

    /**
     * @return Common\Service\Table\TableBuilder
     */
    private function getTable()
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-community-licences', $this->getTableData());
    }

    /**
     * @return array
     */
    private function getTableData()
    {
        $query = [
            'licence' => $this->getLicenceId(),
            'status' => $this->filters['status'],
            'sort' => 'issueNo',
            'order' => 'DESC'
        ];

        return $this->getServiceLocator()->get('Entity\CommunityLic')->getList($query);
    }

    /**
     * Get form data
     *
     * @return array
     */
    private function getFormData()
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getById($this->getLicenceId());
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @return array
     */
    private function formatDataForForm($data)
    {
        return [
            'data' => [
                'totalCommunityLicences' => $data['totCommunityLicences']
            ]
        ];
    }

    /**
     * Hide Add Office Licence action, if necessary
     *
     * @param \Common\Service\Table\TableBuilder $table
     * @return \Common\Service\Table\TableBuilder
     */
    protected function alterTable($table)
    {
        $officeCopy = $this->getServiceLocator()->get('Entity\CommunityLic')->getOfficeCopy($this->getLicenceId());
        if ($officeCopy) {
            $table->removeAction('office-licence-add');
        }
        if (
                !$this->checkTableForLicences(
                    $table,
                    [
                        CommunityLicEntityService::STATUS_PENDING,
                        CommunityLicEntityService::STATUS_ACTIVE,
                        CommunityLicEntityService::STATUS_WITHDRAWN,
                        CommunityLicEntityService::STATUS_SUSPENDED
                    ]
                )
            ) {
            $table->removeAction('void');
        }
        if (
                !$this->checkTableForLicences(
                    $table,
                    [
                        CommunityLicEntityService::STATUS_WITHDRAWN,
                        CommunityLicEntityService::STATUS_SUSPENDED
                    ]
                )
            ) {
            $table->removeAction('restore');
        }
        if (!$this->checkTableForLicences($table, [CommunityLicEntityService::STATUS_ACTIVE])) {
            $table->removeAction('stop');
            $table->removeAction('reprint');
        }

        return $table;
    }

    /**
     * Check table for active licences
     *
     * @param \Common\Service\Table\TableBuilder
     * @param array $statuses
     * @return bool
     */
    protected function checkTableForLicences($table, $statuses)
    {
        $rows = $table->getRows();
        foreach ($rows as $row) {
            if (in_array($row['status']['id'], $statuses)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Office licence add acion
     *
     * @return Zend\Http\Redirect
     */
    public function officeLicenceAddAction()
    {
        $this->getAdapter()->addOfficeCopy($this->getLicenceId());
        $this->addSuccessMessage('internal.community_licence.office_copy_created');
        return $this->redirectToIndex();
    }

    /**
     * Redirect to index
     *
     * @return Zend\Http\Redirect
     */
    protected function redirectToIndex()
    {
        return $this->redirect()->toRouteAjax(
            null,
            ['action' => 'index', $this->getIdentifierIndex() => $this->getIdentifier()]
        );
    }

    /**
     * Add action
     *
     */
    public function addAction()
    {
        $request = $this->getRequest();

        if ($this->isButtonPressed('cancel')) {
            return $this->redirectToIndex();
        }

        $licenceId = $this->getLicenceId();

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\CommunityLicencesAdd');

        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');

        if ($request->isPost()) {

            $this->attachVehicleAuthorityValidator($form);

            $data = (array)$request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $officeCopy = $this->getServiceLocator()->get('Entity\CommunityLic')->getOfficeCopy($licenceId);
                if (!$officeCopy) {
                    $this->getAdapter()->addOfficeCopy($licenceId);
                }

                $this->getAdapter()->addCommunityLicences($licenceId, $data['data']['total']);
                $this->getServiceLocator()->get('Entity\Licence')->updateCommunityLicencesCount($licenceId);

                $this->addSuccessMessage('internal.community_licence.licences_created');

                return $this->redirectToIndex();
            }
        }
        return $this->render($view);
    }

    /**
     * Attach vehicle authority validator
     *
     * @param Zend\Form\Form $form
     */
    protected function attachVehicleAuthorityValidator($form)
    {
        $totalLicences = $this->getServiceLocator()
            ->get('Entity\CommunityLic')
            ->getValidLicences($this->getLicenceId())['Count'];

        $totalVehicleAuthority = $this->getAdapter()->getTotalAuthority($this->getIdentifier());
        $totalVehicleAuthorityValidator = $this->getServiceLocator()->get('totalVehicleAuthorityValidator');
        $totalVehicleAuthorityValidator->setTotalLicences($totalLicences);
        $totalVehicleAuthorityValidator->setTotalVehicleAuthority($totalVehicleAuthority);

        $form->getInputFilter()
            ->get('data')
            ->get('total')
            ->getValidatorChain()
            ->attach($totalVehicleAuthorityValidator);
    }

    /**
     * Add action
     *
     */
    public function voidAction()
    {
        $request = $this->getRequest();

        $ids = explode(',', $this->params('child_id'));
        if (!$this->allowToProcess($ids)) {
            $this->addErrorMessage('internal.community_licence.void_not_allowed');
            return $this->redirectToIndex();
        }
        if (!$request->isPost()) {
            $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\CommunityLicencesVoid');
            $view = new ViewModel(['form' => $form]);
            $view->setTemplate('partials/form');
            return $this->render($view);
        }

        if (!$this->isButtonPressed('cancel')) {
            $this->voidLicences($ids);
            $this->addSuccessMessage('internal.community_licence.licences_voided');
        }
        return $this->redirectToIndex();
    }

    /**
     * Void licences
     *
     * @param array $ids
     */
    protected function voidLicences($ids)
    {
        $licenceId = $this->getLicenceId();
        $licences = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getCommunityLicencesByLicenceIdAndIds($licenceId, $ids);

        $data = [
            'status' => CommunityLicEntityService::STATUS_VOID,
            'expiredDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(),
            'licence' => $licenceId
        ];
        $dataToVoid = [];
        foreach ($licences as $licence) {
            $dataToVoid[] = array_merge($licence, $data);
        }
        $this->getServiceLocator()->get('Entity\CommunityLic')->multiUpdate($dataToVoid);
        $this->getServiceLocator()->get('Entity\Licence')->updateCommunityLicencesCount($licenceId);
    }

    /**
     * Check if selected licences allow to be voided
     *
     * @param string $ids
     */
    protected function allowToProcess($ids)
    {
        $allow = true;
        $licenceId = $this->getLicenceId();
        if ($this->hasOfficeCopy($licenceId, $ids)) {
            $allValidLicences = $this->getServiceLocator()
                ->get('Entity\CommunityLic')
                ->getValidLicences($licenceId);
            foreach ($allValidLicences['Results'] as $validLicence) {
                if (!in_array($validLicence['id'], $ids)) {
                    $allow = false;
                    break;
                }
            }
        }
        return $allow;
    }

    /**
     * Check if selected licences allow to be restored
     *
     * @param string $ids
     */
    protected function allowToRestore($ids)
    {
        $licenceId = $this->getLicenceId();
        if (!$this->hasOfficeCopy($licenceId, $ids)) {
            $officeCopy = $this->getServiceLocator()
                ->get('Entity\CommunityLic')
                ->getOfficeCopy($licenceId);
            if (
                    $officeCopy['status']['id'] == CommunityLicEntityService::STATUS_WITHDRAWN ||
                    $officeCopy['status']['id'] == CommunityLicEntityService::STATUS_SUSPENDED
                ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Restore action
     *
     */
    public function restoreAction()
    {
        $request = $this->getRequest();

        $ids = explode(',', $this->params('child_id'));
        if (!$this->allowToRestore($ids)) {
            $this->addErrorMessage('internal.community_licence.restore_not_allowed');
            return $this->redirectToIndex();
        }
        if (!$request->isPost()) {
            $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\CommunityLicencesRestore');
            $view = new ViewModel(['form' => $form]);
            $view->setTemplate('partials/form');
            return $this->render($view);
        }
        if (!$this->isButtonPressed('cancel')) {
            $this->restoreLicences($ids);
            $this->addSuccessMessage('internal.community_licence.licences_restored');
        }
        return $this->redirectToIndex();
    }

    /**
     * Restore licences
     *
     * @param array $ids
     */
    protected function restoreLicences($ids)
    {
        $licenceId = $this->getLicenceId();
        $licences = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getCommunityLicencesByLicenceIdAndIds($licenceId, $ids);

        $dataToRestore = [];
        foreach ($licences as $licence) {
            if ($licence['specifiedDate']) {
                $data = [
                    'status' => CommunityLicEntityService::STATUS_ACTIVE,
                    'expiredDate' => null
                ];
            } else {
                $data = [
                    'status' => CommunityLicEntityService::STATUS_PENDING,
                    'expiredDate' => null
                ];
            }
            $dataToRestore[] = array_merge($licence, $data);
        }
        $this->getServiceLocator()->get('Entity\CommunityLic')->multiUpdate($dataToRestore);
        $this->getServiceLocator()->get('Entity\CommunityLicSuspension')->deleteSuspensionsAndReasons($ids);
        $this->getServiceLocator()->get('Entity\CommunityLicWithdrawal')->deleteWithdrawalsAndReasons($ids);
    }

    /**
     * Stop action
     *
     */
    public function stopAction()
    {
        if ($this->isButtonPressed('cancel')) {
            return $this->redirectToIndex();
        }
        $request = $this->getRequest();

        $ids = explode(',', $this->params('child_id'));
        if (!$this->allowToStop($ids)) {
            $this->addErrorMessage('internal.community_licence.stop_not_allowed');
            return $this->redirectToIndex();
        }

        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\CommunityLicencesStop');
        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');
        $this->getServiceLocator()->get('Script')->loadFile('community-licence-stop');

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->stopLicences($ids, $form->getData());
                return $this->redirectToIndex();
            }
        }

        return $this->render($view);
    }

    public function reprintAction()
    {
        if ($this->getRequest()->isPost() && $this->isButtonPressed('cancel')) {
            return $this->redirectToIndex();
        }

        $licenceId  = $this->getLicenceId();
        $ids = explode(',', $this->params('child_id'));

        if (!$this->allowToReprint($ids)) {
            $this->addErrorMessage('internal.community_licence.reprint_not_allowed');
            return $this->redirectToIndex();
        }

        if ($this->getRequest()->isPost()) {
             // 1. Void existing licences
            $this->voidLicences($ids);

            // 2. Create new licences with the same issue numbers
            $issueNos = $this->getIssueNumbersForLicences($ids);
            $this->getAdapter()->addCommunityLicencesWithIssueNos($licenceId, $issueNos);

            $this->addSuccessMessage('internal.community_licence.licences_reprinted');
            return $this->redirectToIndex();
        }

        return $this->renderConfirmation('internal.community_licence.confirm_reprint_licences');
    }

    /**
     * Check if selected licences can be reprinted
     *
     * @param string $ids
     * @return boolean true iff all are active
     */
    protected function allowToReprint($ids)
    {
        $licenceId = $this->getLicenceId();
        $activeLicences = $this->getServiceLocator()->get('Entity\CommunityLic')
            ->getActiveLicences($licenceId);

        $activeIds = [];
        foreach ($activeLicences['Results'] as $licence) {
            $activeIds[] = $licence['id'];
        }

        $notActive = array_diff($ids, $activeIds);
        return empty($notActive);
    }

    protected function getIssueNumbersForLicences($ids)
    {
        return array_map(
            function ($licence) {
                return $licence['issueNo'];
            },
            $this->getServiceLocator()->get('Entity\CommunityLic')->getByIds($ids)
        );
    }

    /**
     * Check if selected licences allow to be suspended / withdrawn
     *
     * @param string $ids
     */
    protected function allowToStop($ids)
    {
        $licenceId = $this->getLicenceId();
        if ($this->hasOfficeCopy($licenceId, $ids)) {
            $allValidLicences = $this->getServiceLocator()
                ->get('Entity\CommunityLic')
                ->getValidLicences($licenceId);
            foreach ($allValidLicences['Results'] as $validLicence) {
                if (

                        ($validLicence['status']['id'] == CommunityLicEntityService::STATUS_PENDING) ||

                        (
                           $validLicence['status']['id'] == CommunityLicEntityService::STATUS_ACTIVE &&
                           !in_array($validLicence['id'], $ids)
                        )

                    ) {

                    return false;

                }
            }
        }
        return true;
    }

    /**
     * Check if office copy was selected
     *
     * @param int $licenceId
     * @param array $ids
     * @return bool
     */
    protected function hasOfficeCopy($licenceId, $ids)
    {
        $licences = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getCommunityLicencesByLicenceIdAndIds($licenceId, $ids);
        foreach ($licences as $licence) {
            if ($licence['issueNo'] === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Suspend or withdrawn licences
     *
     * @param array $ids
     * @param array $formattedData
     * @return bool
     */
    protected function stopLicences($ids, $formattedData)
    {
        $type = $formattedData['data']['type'] === 'N' ? 'withdrawal' : 'suspension';

        // prepare common data for community licence depending of selected asction
        if ($type == 'withdrawal') {
            $comLicData = [
              'status' => CommunityLicEntityService::STATUS_WITHDRAWN,
              'expiredDate' => $this->getServiceLocator()->get('Helper\Date')->getDate()
            ];
            $message = 'internal.community_licence.licences_withdrawn';
            $suspensionOrWithrawalService = 'Entity\CommunityLicWithdrawal';
            $reasonService = 'Entity\CommunityLicWithdrawalReason';
        } else {
            $comLicData = [
                'status' => CommunityLicEntityService::STATUS_SUSPENDED
             ];
            $message = 'internal.community_licence.licences_suspended';
            $suspensionOrWithrawalService = 'Entity\CommunityLicSuspension';
            $reasonService = 'Entity\CommunityLicSuspensionReason';
        }

        // fetch community licences by ids to get version field
        $comLics = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getCommunityLicencesByLicenceIdAndIds($this->getLicenceId(), $ids);

        // prepare data to save all community licences and all suspension/withdrawal records at once
        $comLicsToSave = [];
        $comLicsWs = [];
        foreach ($comLics as $comLic) {
            $comLicsToSave[] = array_merge(
                $comLicData, ['id' => $comLic['id'], 'version' => $comLic['version']]
            );
            $data = [
                'communityLic' => $comLic['id']
            ];
            if ($type == 'suspension') {
                $data['startDate'] = $formattedData['dates']['startDate'];
                $data['endDate'] = $formattedData['dates']['endDate'];
            }
            $comLicsWs[] = $data;
        }
        $this->getServiceLocator()->get('Entity\CommunityLic')->multiUpdate($comLicsToSave);

        $comLicsWs['_OPTIONS_'] = ['multiple' => true];
        $result = $this->getServiceLocator()->get($suspensionOrWithrawalService)->save($comLicsWs);

        if (!is_array($result['id'])) {
            $result['id'] = [$result['id']];
        }
        // prepare to save all withdrawal/suspension reasons at once
        $reasons = [];
        foreach ($result['id'] as $id) {
            foreach ($formattedData['data']['reason'] as $reason) {
                $data = [
                    'communityLic' . ucfirst($type) => $id,
                    'type' => $reason
                ];
                $reasons[] = $data;
            }
        }
        $reasons['_OPTIONS_'] = ['multiple' => true];
        $this->getServiceLocator()->get($reasonService)->save($reasons);
        $this->addSuccessMessage($message);
    }

    protected function renderConfirmation($message)
    {
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('GenericConfirmation', $this->getRequest());

        $form->get('messages')->get('message')->setValue($message);

        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');

        return $this->render($view);
    }
}
