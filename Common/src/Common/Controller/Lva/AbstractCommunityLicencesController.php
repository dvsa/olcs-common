<?php

/**
 * Shared logic between Community Licences controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\CommunityLicEntityService;

/**
 * Shared logic between Community Licences controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractCommunityLicencesController extends AbstractController
{
    protected $section = 'community_licences';

    protected $defaultFilters = [
        'status' => [
            CommunityLicEntityService::STATUS_PENDING,
            CommunityLicEntityService::STATUS_VALID,
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

        $formHelper->populateFormTable($form->get('table'), $this->getTable());

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
}
