<?php

/**
 * Shared logic between Business Details controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Traits\Lva;

use Common\Service\Entity\OrganisationService;

/**
 * Shared logic between Business Details controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait BusinessDetailsTrait
{
    /**
     * Business details section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        $orgId = $this->getCurrentOrganisationId();
        // we *always* want to get org data because we rely on it in
        // alterForm which is called irrespective of whether we're doing
        // a GET or a POST
        $orgData = $this->getEntityService('Organisation')->getBusinessDetailsData($orgId);

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($orgData);
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\BusinessDetails');

        $this->alterForm($form, $orgData)
            ->setData($data);

        if ($form->has('table')) {
            $this->populateTable($form, $orgId);
        }

        if ($request->isPost()) {
            /**
             * we'll re-use this in a few places, so cache the lookup
             * just for the sake of legibility
             */
            $tradingNames = isset($data['data']['tradingNames']) ? $data['data']['tradingNames'] : array();

            if (isset($data['data']['companyNumber']['submit_lookup_company'])) {
                /**
                 * User has pressed 'Find company' on registered company number
                 */
                if (strlen(trim($data['data']['companyNumber']['company_number'])) != 8) {

                    $form->get('data')->get('companyNumber')->setMessages(
                        array(
                            'company_number' => array(
                                'The input must be 8 characters long'
                            )
                        )
                    );

                } else {
                    $result = $this->getServiceLocator()
                        ->get('CompaniesHouse')
                        ->search('numberSearch', $data['data']['companyNumber']['company_number']);

                    if ($result['Count'] == 1) {

                        $companyName = $result['Results'][0]['CompanyName'];
                        $form->get('data')->get('name')->setValue($companyName);

                    } else {

                        $form->get('data')->get('companyNumber')->setMessages(
                            array(
                                'company_number' => array(
                                    'Sorry, we couldn\'t find any matching companies, please try again or enter your '
                                    . 'details manually below'
                                )
                            )
                        );
                    }
                }
            } elseif (isset($tradingNames['submit_add_trading_name'])) {
                /**
                 * User has pressed 'Add another' on trading names
                 */
                $form->setValidationGroup(array('data' => ['tradingNames']));

                if ($form->isValid()) {

                    // remove existing entries from collection and check for empty entries
                    $names = [];
                    foreach ($tradingNames['trading_name'] as $key => $val) {
                        if (strlen(trim($val['text'])) > 0) {
                            $names[] = $val;
                        }
                    }
                    $names[] = ['text' => ''];

                    $form->get('data')->get('tradingNames')->get('trading_name')->populateValues($names);
                }
            } elseif ($form->isValid()) {
                /**
                 * Normal submission; save the form data
                 */
                if (isset($tradingNames['trading_name']) && count($tradingNames['trading_name'])) {
                    $tradingNames = $this->formatTradingNamesDataForSave($orgId, $data);
                    $this->getEntityService('TradingNames')->save($tradingNames);
                }

                $saveData = $this->formatDataForSave($data);
                $saveData['id'] = $orgId;
                $this->getEntityService('Organisation')->save($saveData);

                if (isset($data['table']['action'])) {
                    $action = strtolower($data['table']['action']);

                    if ($action === 'add') {
                        $routeParams = array(
                            'action' => 'add'
                        );
                    } elseif ($action === 'edit') {
                        $routeParams = array(
                            'action' => 'edit',
                            'child_id' => $data['table']['id']
                        );
                    } else {
                        $routeParams = array();
                        $this->getEntityService('CompanySubsidiary')
                            ->delete($data['table']['id']);
                    }

                    return $this->redirect()->toRoute(
                        'lva-' . $this->lva . '/business_details',
                        $routeParams,
                        array(),
                        true
                    );
                }

                return $this->completeSection('business_details');
            }
        }

        return $this->render('business_details', $form);
    }

    public function addAction()
    {
        return $this->addOrEdit(true);
    }

    public function editAction()
    {
        return $this->addOrEdit(false);
    }

    private function addOrEdit($add = true)
    {
        $mode = $add ? 'add' : 'edit';
        $orgId = $this->getCurrentOrganisationId();
        $request = $this->getRequest();

        $data = array();
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->formatSubsidiaryDataForForm(
                $this->getEntityService('CompanySubsidiary')->getById($this->params('child_id'))
            );
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\BusinessDetailsSubsidiaryCompany');

        if ($request->isPost() && $form->isValid()) {
            $data = $this->formatSubsidiaryDataForSave($data);
            $data['organisation'] = $orgId;

            $this->getEntityService('CompanySubsidiary')->save($data);

            // we can't just opt-in to all existing route params because
            // we might have a child ID if we're editing; if so we *don't*
            // want that in the redirect or we'll end up back on the same page
            $routeParams = array(
                'id' => $this->params('id')
            );
            if ($this->isButtonPressed('addAnother')) {
                $routeParams['action'] = 'add';
            }
            return $this->redirect()->toRoute(
                'lva-' . $this->lva . '/business_details',
                $routeParams
            );
        }

        return $this->render($mode . '_subsidiary_company', $form);
    }

    private function formatTradingNamesDataForSave($organisationId, $data)
    {
        $tradingNames = [];

        foreach ($data['data']['tradingNames']['trading_name'] as $tradingName) {
            if (trim($tradingName['text']) !== '') {
                $tradingNames[] = [
                    'name' => $tradingName['text']
                ];
            }
        }

        $data['tradingNames'] = $tradingNames;

        return array(
            'organisation' => $organisationId,
            'licence' => $this->getLicenceId(),
            'tradingNames' => $tradingNames
        );
    }

    /**
     * Override built-in cancel functionality; need
     * to check if we're on a sub action
     *
     * Might be able to squeeze this into the abstract,
     * will see how consistent other sub actions are first
     */
    protected function handleCancelRedirect($lvaId)
    {
        if ($this->params('action') !== 'index') {
            return $this->redirect()->toRoute(
                'lva-' . $this->lva . '/business_details',
                array('id' => $lvaId)
            );
        }
        return parent::handleCancelRedirect($lvaId);
    }

    /**
     * Format data for save
     *
     * @param array $data
     * @return array
     */
    private function formatDataForSave($data)
    {
        return array(
            'version' => $data['version'],
            'companyOrLlpNo' => isset($data['data']['companyNumber']['company_number'])
            ? $data['data']['companyNumber']['company_number']
            : null,
            'name' => isset($data['data']['name']) ? $data['data']['name'] : null
        );
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @return array
     */
    private function formatDataForForm($data)
    {
        $tradingNames = array();
        foreach ($data['tradingNames'] as $tradingName) {
            $tradingNames[] = array('text' => $tradingName['name']);
        }
        return array(
            'version' => $data['version'],
            'data' => array(
                'companyNumber' => array(
                    'company_number' => $data['companyOrLlpNo']
                ),
                'tradingNames' => array(
                    'trading_name' => $tradingNames
                ),
                'name' => $data['name'],
                'type' => $data['type']['id']
            )
        );
    }

    /**
     * Format subsidiary data for save
     *
     * @param array $data
     * return array
     */
    private function formatSubsidiaryDataForSave($data)
    {
        return $data['data'];
    }

    /**
     * Format subsidiary data for form
     *
     * @param array $data
     * return array
     */
    private function formatSubsidiaryDataForForm($data)
    {
        return array('data' => $data);
    }

    /**
     * Alter form based on available data
     *
     * @param \Zend\Form\Form $form
     * @param array $data
     * @return \Zend\Form\Form
     */
    private function alterForm($form, $data)
    {
        $orgType = $data['type']['id'];

        $fieldset = $form->get('data');

        $element = $fieldset->get('editBusinessType');
        $element->setOptions(
            array_merge(
                $element->getOptions(),
                array('route' => 'lva-' . $this->lva . '/business_type')
            )
        );

        // we have to manually set the business type, otherwise if this
        // was a POST it won't come through (it's a disabled element)
        // and will default to the first value (limited company)
        $fieldset->get('type')->setValue($orgType);

        switch ($orgType) {
            case OrganisationService::ORG_TYPE_REGISTERED_COMPANY:
            case OrganisationService::ORG_TYPE_LLP:
                // no-op; the full form is fine
                break;

            case OrganisationService::ORG_TYPE_SOLE_TRADER:
                $fieldset->remove('name');
                $fieldset->remove('companyNumber');

                $form->remove('table');
                $form->getInputFilter()->get('data')->remove('name');
                break;

            case OrganisationService::ORG_TYPE_PARTNERSHIP:
                $fieldset->remove('companyNumber');
                $fieldset->get('name')->setLabel($fieldset->get('name')->getLabel() . '.partnership');
                $form->remove('table');
                break;

            case OrganisationService::ORG_TYPE_OTHER:
                $fieldset->remove('companyNumber')->remove('tradingNames');
                $fieldset->get('name')->setLabel($fieldset->get('name')->getLabel() . '.other');
                $form->remove('table');
                break;
        }

        return $form;
    }

    private function populateTable($form, $orgId)
    {
        $tableData = $this->getEntityService('CompanySubsidiary')
            ->getAllForOrganisation($orgId);

        $table = $this->getServiceLocator()
            ->get('Table')
            ->buildTable(
                // @TODO rename / move table? This trait is re-used
                // across app / var / licences...
                'application_your-business_business_details-subsidiaries',
                $tableData,
                array(), // params?
                false
            );

        $column = $table->getColumn('name');
        $column['type'] = $this->lva;
        $table->setColumn('name', $column);

        $form->get('table')  // fieldset
            ->get('table')   // element
            ->setTable($table);
    }
}
