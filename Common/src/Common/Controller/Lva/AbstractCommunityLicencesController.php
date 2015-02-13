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

        $this->getServiceLocator()->get('Script')->loadFile('forms/filter');

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
     * @param \Common\Service\Table\TableBuilder
     * @return \Common\Service\Table\TableBuilder
     */
    protected function alterTable($table)
    {
        $officeCopy = $this->getServiceLocator()->get('Entity\CommunityLic')->getOfficeCopy($this->getLicenceId());
        if ($officeCopy) {
            $table->removeAction('office-licence-add');
        }

        return $table;
    }

    /**
     * Office licence add acion
     * 
     * @return Zend\Http\Redirect
     */
    public function officeLicenceAddAction()
    {
        $this->getAdapter()->addOfficeCopy($this->getLicenceId());
        $translator = $this->getServiceLocator()->get('translator');
        $this->addSuccessMessage($translator->translate('internal.community_licence.office_copy_created'));
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

                $translator = $this->getServiceLocator()->get('translator');
                $this->addSuccessMessage($translator->translate('internal.community_licence.licences_created'));

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
        $totalDiscs = $this->getServiceLocator()
            ->get('Entity\PsvDisc')
            ->getNotCeasedDiscs($this->getLicenceId())['Count'];

        $totalVehicleAuthority = $this->getAdapter()->getTotalAuthority($this->getIdentifier());
        $totalVehicleAuthorityValidator = $this->getServiceLocator()->get('totalVehicleAuthorityValidator');
        $totalVehicleAuthorityValidator->setTotalDiscs($totalDiscs);
        $totalVehicleAuthorityValidator->setTotalVehicleAuthority($totalVehicleAuthority);

        $form->getInputFilter()
            ->get('data')
            ->get('total')
            ->getValidatorChain()
            ->attach($totalVehicleAuthorityValidator);
    }
}
