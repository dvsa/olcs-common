<?php

namespace Common\Controller\Lva;

use Common\Form\Form;
use Dvsa\Olcs\Transfer\Query\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Application\CreateOfficeCopy as ApplicationCreateOfficeCopy;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Licence\CreateOfficeCopy as LicenceCreateOfficeCopy;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Application\Create as ApplicationCreateCommunityLic;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Licence\Create as LicenceCreateCommunityLic;
use Dvsa\Olcs\Transfer\Command\CommunityLic\EditSuspension as EditSuspensionDto;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Reprint as ReprintDto;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Restore as RestoreDto;
use Common\Data\Mapper\Lva\CommunityLicence as CommunityLicMapper;
use Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLicences;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Stop as StopDto;
use Common\Controller\Lva\Interfaces\AdapterAwareInterface;
use Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLicence;
use Olcs\Mvc\Controller\ParameterProvider\GenericList;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Common\Service\Table\TableBuilder;
use Laminas\View\Model\ViewModel;
use Common\RefData;

/**
 * Shared logic between Community Licences controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractCommunityLicencesController extends AbstractController implements AdapterAwareInterface
{
    use Traits\CrudTableTrait,
        Traits\AdapterAwareTrait;

    // See OLCS-16655, pagination is to be 50 per page only
    const TABLE_RESULTS_PER_PAGE = 50;

    protected $section = 'community_licences';
    protected $baseRoute = 'lva-%s/community_licences';

    protected $officeCopy = null;

    protected $totCommunityLicences = null;

    protected $defaultFilters = [
        'status' => [
            RefData::COMMUNITY_LICENCE_STATUS_PENDING,
            RefData::COMMUNITY_LICENCE_STATUS_ACTIVE,
            RefData::COMMUNITY_LICENCE_STATUS_WITHDRAWN,
            RefData::COMMUNITY_LICENCE_STATUS_SUSPENDED
        ]
    ];

    protected $filters = [];

    /**
     * Community Licences section
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function indexAction()
    {
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $crudAction = $this->getCrudAction([$data['table']]);

            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction, ['add', 'add office licence']);
            }

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
        $this->alterFormForGoodsOrPsv($form);
        $this->alterFormForLva($form);
        $data = $this->formatDataForForm($this->getFormData());
        $form->setData($data);

        $this->getServiceLocator()->get('Script')->loadFiles(['forms/filter', 'community-licence']);

        return $this->render('community_licences', $form, ['filterForm' => $filterForm]);
    }

    /**
     * Alter form for goods or psv
     */
    private function alterFormForGoodsOrPsv(Form $form)
    {
        $response = $this->handleQuery(
            Licence::create(['id' => $this->getLicenceId()])
        );
        $licence = $response->getResult();

        if ($licence['goodsOrPsv']['id'] == RefData::LICENCE_CATEGORY_PSV) {
            $activeLicencesElement = $form->get('data')->get('totalCommunityLicences');
            $activeLicencesElement->setLabel(
                $activeLicencesElement->getLabel() . '.psv'
            );
        }
    }

    /**
     * Get filter form
     *
     * @return \Laminas\Form\Form
     */
    private function getFilterForm()
    {
        /** @var Form $form */
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\CommunityLicenceFilter', false);

        $lva = ($this->lva !== 'variation')? $this->lva :'application';

        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                $this->getBaseRoute(),
                [$lva => $this->getIdentifier()]
            )
        );

        return $form;
    }

    /**
     * Get form
     *
     * @return \Laminas\Form\FormInterface
     */
    private function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        /** @var \Laminas\Form\FormInterface $form */
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();

        $table = $this->alterTable($this->getTableConfig());
        $formHelper->populateFormTable($form->get('table'), $table);
        $this->getServiceLocator()->get('Helper\Form')->setFormActionFromRequest($form, $this->getRequest());

        return $form;
    }

    /**
     * Get Table
     *
     * @return TableBuilder
     */
    private function getTableConfig()
    {
        $licenceData = $this->getTableData();

        /** @var TableBuilder $table */
        $table = $this->table()->buildTable(
            'community.licence',
            $licenceData,
            $this->params()->fromQuery(),
            false
        );

        if ($licenceData['count'] > 0) {
            $table->addAction(
                'annul',
                ['class' => 'action--secondary', 'value' => 'Annul']
            );
            $table->addAction(
                'stop',
                ['class' => 'action--secondary', 'value' => 'Stop']
            );
            $table->addAction(
                'reprint',
                ['class' => 'action--secondary', 'value' => 'Reprint']
            );
        }

        return $table;
    }

    /**
     * Get Table Data
     *
     * @return array
     */
    private function getTableData()
    {
        $paramProvider = new GenericList(['licence']);
        $paramProvider->setParams($this->plugin('params'));

        $listParams = $paramProvider->provideParameters();
        $listParams['statuses'] = implode(',', $this->filters['status']);
        $listParams['limit'] = self::TABLE_RESULTS_PER_PAGE;
        $listParams['licence'] = $this->getLicenceId();

        $response = $this->handleQuery(CommunityLicences::create($listParams));

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        $results = [];

        if ($response->isOk()) {
            $results = $response->getResult();
            $this->officeCopy = $results['extra']['officeCopy'];
            $this->totCommunityLicences = $results['extra']['totCommunityLicences'];
        }

        return $results;
    }

    /**
     * Get form data
     *
     * @return array
     */
    private function getFormData()
    {
        return ['totCommunityLicences' => $this->totCommunityLicences];
    }

    /**
     * Format data for form
     *
     * @param array $data Data
     *
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
     * @param \Common\Service\Table\TableBuilder $table Table
     *
     * @return \Common\Service\Table\TableBuilder
     */
    protected function alterTable($table)
    {
        $officeCopy = $this->officeCopy;
        if ($officeCopy) {
            $table->removeAction('office-licence-add');
        }
        if (!$this->checkTableForLicences(
            $table,
            [
                RefData::COMMUNITY_LICENCE_STATUS_PENDING,
                RefData::COMMUNITY_LICENCE_STATUS_ACTIVE,
                RefData::COMMUNITY_LICENCE_STATUS_WITHDRAWN,
                RefData::COMMUNITY_LICENCE_STATUS_SUSPENDED
            ]
        )) {
            $table->removeAction('void');
        }
        if (!$this->checkTableForLicences(
            $table,
            [
                RefData::COMMUNITY_LICENCE_STATUS_WITHDRAWN,
                RefData::COMMUNITY_LICENCE_STATUS_SUSPENDED
            ]
        )) {
            $table->removeAction('restore');
        }
        if (!$this->checkTableForLicences($table, [RefData::COMMUNITY_LICENCE_STATUS_ACTIVE])) {
            $table->removeAction('stop');
            $table->removeAction('reprint');
        }

        return $table;
    }

    /**
     * Check table for active licences
     *
     * @param \Common\Service\Table\TableBuilder $table    Table
     * @param array                              $statuses Statuses
     *
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
     * @return \Laminas\Http\Response
     */
    public function addOfficeLicenceAction()
    {
        if ($this->lva === 'licence') {
            $create = [
                'licence' => $this->getLicenceId(),
            ];
            $dto = LicenceCreateOfficeCopy::create($create);
        } else {
            $create = [
                'licence' =>  $this->getLicenceId(),
                'identifier' => $this->getIdentifier()
            ];
            $dto = ApplicationCreateOfficeCopy::create($create);
        }
        return $this->processDto($dto, 'internal.community_licence.office_copy_created');
    }

    /**
     * Redirect to index
     *
     * @return \Laminas\Http\Response
     */
    protected function redirectToIndex()
    {
        return $this->redirect()->toRouteAjax(
            null,
            ['action' => 'index', $this->getIdentifierIndex() => $this->getIdentifier()],
            ['query' => $this->getRequest()->getQuery()->toArray()]
        );
    }

    /**
     * Add action
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function addAction()
    {
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if ($this->isButtonPressed('cancel')) {
            return $this->redirectToIndex();
        }

        $licenceId = $this->getLicenceId();

        /** @var \Common\Service\Helper\FormHelperService $formHelper */
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\CommunityLicencesAdd');
        $formHelper->setFormActionFromRequest($form, $request);

        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');

        if ($request->isPost()) {
            $identifier = $this->getIdentifier();
            $data = (array)$request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                if ($this->lva === 'licence') {
                    $create = [
                        'licence' => $licenceId,
                        'totalLicences' => $data['data']['total'],
                    ];
                    $dto = LicenceCreateCommunityLic::create($create);
                } else {
                    $create = [
                        'licence' => $licenceId,
                        'totalLicences' => $data['data']['total'],
                        'identifier' => $identifier
                    ];
                    $dto = ApplicationCreateCommunityLic::create($create);
                }
                return $this->processDto($dto, 'internal.community_licence.licences_created');
            }
        }
        return $this->render($view);
    }

    /**
     * Annul action
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function annulAction()
    {
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        $ids = explode(',', $this->params('child_id'));
        if (!$request->isPost()) {
            $formHelper = $this->getServiceLocator()->get('Helper\Form');
            $form = $formHelper->createForm('Lva\CommunityLicencesAnnul');
            $formHelper->setFormActionFromRequest($form, $this->getRequest());

            $view = new ViewModel(['form' => $form]);
            $view->setTemplate('partials/form');

            return $this->render($view);
        }

        if (!$this->isButtonPressed('cancel')) {
            $void = [
                'licence' => $this->getLicenceId(),
                'communityLicenceIds' => $ids,
                'checkOfficeCopy' => true
            ];

            if ($this->lva !== 'licence') {
                $void['application'] = $this->getIdentifier();
            }

            return $this->processDto(
                TransferCmd\CommunityLic\Annul::create($void),
                'internal.community_licence.licences_annulled'
            );
        }
        return $this->redirectToIndex();
    }

    /**
     * Restore action
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function restoreAction()
    {
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $formHelper = $this->getServiceLocator()->get('Helper\Form');
            $form = $formHelper->createForm('Lva\CommunityLicencesRestore');
            $formHelper->setFormActionFromRequest($form, $this->getRequest());
            $view = new ViewModel(['form' => $form]);
            $view->setTemplate('partials/form');
            return $this->render($view);
        }
        if (!$this->isButtonPressed('cancel')) {
            $restore = [
                'licence' => $this->getLicenceId(),
                'communityLicenceIds' => explode(',', $this->params('child_id'))
            ];
            return $this->processDto(RestoreDto::create($restore), 'internal.community_licence.licences_restored');
        }
        return $this->redirectToIndex();
    }

    /**
     * Stop action
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function stopAction()
    {
        if ($this->isButtonPressed('cancel')) {
            return $this->redirectToIndex();
        }
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        $form = $this->getStopForm();
        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $form->setData($data);
            $this->alterStopForm($form);

            if ($form->isValid()) {
                $formattedData = $form->getData();
                $type = $formattedData['data']['type'] === 'N' ? 'withdrawal' : 'suspension';
                $message = ($type == 'withdrawal') ? 'internal.community_licence.licences_withdrawn' :
                    'internal.community_licence.licences_suspended';

                $stop = [
                    'licence' => $this->getLicenceId(),
                    'communityLicenceIds' => explode(',', $this->params('child_id')),
                    'type' => $type,
                    'startDate' =>
                        isset($formattedData['dates']['startDate']) ? $formattedData['dates']['startDate'] : null,
                    'endDate' =>
                        isset($formattedData['dates']['endDate']) ? $formattedData['dates']['endDate'] : null,
                    'reasons' => $formattedData['data']['reason']
                ];

                if ($this->lva !== 'licence') {
                    $stop['application'] = $this->getIdentifier();
                }

                return $this->processDto(StopDto::create($stop), $message);
            }
        }

        $this->placeholder()->setPlaceholder('contentTitle', 'Stop community licence');
        $this->getServiceLocator()->get('Script')->loadFile('community-licence-stop');
        return $this->render($view);
    }

    /**
     * Edit action
     *
     * @return mixed
     */
    public function editAction()
    {
        if ($this->isButtonPressed('cancel')) {
            return $this->redirectToIndex();
        }

        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        $form = $this->getEditSuspensionForm();
        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');

        if ($request->isPost()) {
            $data = (array) $request->getPost();
        } else {
            $data = $this->getCommunityLicenceData();
            if ($data instanceof Response) {
                return $data;
            }
        }

        $form->setData($data);
        $this->alterEditSuspensionForm($form);

        if ($request->isPost() && $form->isValid()) {
            $dtoData = CommunityLicMapper::mapFromForm($data, $this->params('child_id'));
            $response = $this->sendCommand(EditSuspensionDto::create($dtoData));
            if ($response->isOk()) {
                $result = $response->getResult();
                if (isset($result['messages']) && count($result['messages'])) {
                    $successMessage = $result['messages'][0];
                    $this->addSuccessMessage($successMessage);
                }
                return $this->redirectToIndex();
            }
            $this->displayErrors($response);
        }
        $this->placeholder()->setPlaceholder('contentTitle', 'Community licence suspension details');

        return $this->render($view);
    }

    /**
     * Get edit suspension form
     *
     * @see \Common\Form\Model\Form\Lva\CommunityLicencesStop
     *
     * @return \Laminas\Form\FormInterface
     */
    protected function getEditSuspensionForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\CommunityLicencesEditSuspension');
        $formHelper->setFormActionFromRequest($form, $this->getRequest());
        return $form;
    }

    /**
     * Alter edit suspension form
     *
     * @param \Laminas\Form\FormInterface $form form
     *
     * @return void
     */
    protected function alterEditSuspensionForm($form)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $status = $form->get('data')->get('status')->getValue();

        if ($status === RefData::COMMUNITY_LICENCE_STATUS_SUSPENDED) {
            /** @var \Common\Form\Elements\Custom\DateSelect $startDate */
            $startDate = $form->get('dates')->get('startDate');
            $startDate->getDayElement()->setAttribute('readonly', 'readonly');
            $startDate->getMonthElement()->setAttribute('readonly', 'readonly');
            $startDate->getYearElement()->setAttribute('readonly', 'readonly');
            $formHelper->removeValidator(
                $form,
                'dates->startDate',
                \Dvsa\Olcs\Transfer\Validators\DateInFuture::class
            );
        }
    }

    /**
     * Get community licence data
     *
     * @return array
     */
    protected function getCommunityLicenceData()
    {
        $response = $this->handleQuery(CommunityLicence::create(['id' => $this->params('child_id')]));

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        $result = [];
        if ($response->isOk()) {
            $result = CommunityLicMapper::mapFromResult($response->getResult());
        }
        return $result;
    }

    /**
     * Get stop form @see \Common\Form\Model\Form\Lva\CommunityLicencesStop
     *
     * @return \Laminas\Form\FormInterface
     */
    protected function getStopForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\CommunityLicencesStop');
        $formHelper->setFormActionFromRequest($form, $this->getRequest());
        return $form;
    }

    /**
     * Alter stop form
     *
     * @param \Laminas\Form\FormInterface $form form
     *
     * @return void
     */
    protected function alterStopForm($form)
    {
        if ($form->get('data')->get('type')->getValue() === 'N') {
            $this->getServiceLocator()->get('Helper\Form')
                ->disableValidation(
                    $form->getInputFilter()->get('dates')->get('startDate')
                );
        }
    }

    /**
     * Action: Reprint
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function reprintAction()
    {
        if ($this->getRequest()->isPost() && $this->isButtonPressed('cancel')) {
            return $this->redirectToIndex();
        }

        if ($this->getRequest()->isPost()) {
            $reprint = [
                'licence' => $this->getLicenceId(),
                'communityLicenceIds' => explode(',', $this->params('child_id'))
            ];

            if ($this->lva === self::LVA_APP) {
                // If reprinting on an Application with an Interim, then need to pass application ID
                $reprint['application'] = $this->getApplicationId();
            }
            return $this->processDto(ReprintDto::create($reprint), 'internal.community_licence.licences_reprinted');
        }

        return $this->renderConfirmation('internal.community_licence.confirm_reprint_licences');
    }

    /**
     * Render Confirmation
     *
     * @param string $message Message
     *
     * @return \Common\View\Model\Section
     */
    protected function renderConfirmation($message)
    {
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('GenericConfirmation', $this->getRequest());

        $form->get('messages')->get('message')->setValue($message);

        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');

        return $this->render($view);
    }

    /**
     * Process dto
     *
     * @param \Dvsa\Olcs\Transfer\Command\AbstractCommand $dto            dto
     * @param string                                      $successMessage success message
     *
     * @return \Laminas\Http\Response
     */
    protected function processDto($dto, $successMessage)
    {
        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->sendCommand($dto);

        if ($response->isOk()) {
            $this->addSuccessMessage($successMessage);
            return $this->redirectToIndex();
        }

        $this->displayErrors($response);
        return $this->redirectToIndex();
    }

    /**
     * Send command
     *
     * @param \Dvsa\Olcs\Transfer\Command\AbstractCommand $dto dto
     *
     * @return \Common\Service\Cqrs\Response
     */
    protected function sendCommand($dto)
    {
        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);
        /** @var \Common\Service\Cqrs\Response $response */
        return $this->getServiceLocator()->get('CommandService')->send($command);
    }

    /**
     * Display errors
     *
     * @param \Common\Service\Cqrs\Response $response response
     *
     * @return void
     */
    protected function displayErrors($response)
    {
        if ($response->isClientError()) {
            $errors = $response->getResult()['messages'];
            foreach ($errors as $error) {
                $this->addErrorMessage($error);
            }
        }

        if ($response->isServerError()) {
            $this->addErrorMessage('unknown-error');
        }
    }
}
