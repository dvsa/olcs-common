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
use Common\Data\Mapper\Lva\CommunityLic as CommunityLicMapper;
use Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLic;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Application\Create as ApplicationCreateCommunityLic;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Licence\Create as LicenceCreateCommunityLic;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Application\CreateOfficeCopy as ApplicationCreateOfficeCopy;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Licence\CreateOfficeCopy as LicenceCreateOfficeCopy;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Reprint as ReprintDto;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Restore as RestoreDto;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Stop as StopDto;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Void as VoidDto;

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

    protected $officeCopy = null;

    protected $totCommunityLicences = null;

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
            'statuses' => implode(',', $this->filters['status']),
            'sort' => 'issueNo',
            'order' => 'DESC'
        ];
        $queryToSend = $this->getServiceLocator()
            ->get('TransferAnnotationBuilder')
            ->createQuery(
                CommunityLic::create($query)
            );

        $response = $this->getServiceLocator()->get('QueryService')->send($queryToSend);
        if ($response->isNotFound()) {
            return $this->notFoundAction();
        }

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        $mappedResults = [];
        if ($response->isOk()) {
            $mapper = new CommunityLicMapper();
            $mappedResults = $mapper->mapFromResult($response->getResult());
            $this->officeCopy = $mappedResults['extra']['officeCopy'];
            $this->totCommunityLicences = $mappedResults['extra']['totCommunityLicences'];
        }
        return $mappedResults;
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
        $officeCopy = $this->officeCopy;
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
     * Add action
     *
     */
    public function voidAction()
    {
        $request = $this->getRequest();

        $ids = explode(',', $this->params('child_id'));
        if (!$request->isPost()) {
            $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\CommunityLicencesVoid');
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
            return $this->processDto(VoidDto::create($void), 'internal.community_licence.licences_voided');
        }
        return $this->redirectToIndex();
    }

    /**
     * Restore action
     *
     */
    public function restoreAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\CommunityLicencesRestore');
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
     */
    public function stopAction()
    {
        if ($this->isButtonPressed('cancel')) {
            return $this->redirectToIndex();
        }
        $request = $this->getRequest();

        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\CommunityLicencesStop');
        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');
        $this->getServiceLocator()->get('Script')->loadFile('community-licence-stop');

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $form->setData($data);
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
                return $this->processDto(StopDto::create($stop), $message);
            }
        }

        return $this->render($view);
    }

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
            return $this->processDto(ReprintDto::create($reprint), 'internal.community_licence.licences_reprinted');
        }

        return $this->renderConfirmation('internal.community_licence.confirm_reprint_licences');
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

    protected function processDto($dto, $successMessage)
    {
        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);
        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);
        if ($response->isOk()) {
            $this->addSuccessMessage($successMessage);
            return $this->redirectToIndex();
        }
        if ($response->isClientError()) {
            $errors = $response->getResult()['messages'];
            foreach ($errors as $error) {
                $this->addErrorMessage($error);
            }
        }
        if ($response->isServerError()) {
            $this->addErrorMessage('unknown-error');
        }
        return $this->redirectToIndex();
    }
}
