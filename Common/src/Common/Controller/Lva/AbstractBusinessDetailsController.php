<?php

/**
 * Shared logic between Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\OrganisationEntityService;
use Common\Service\Helper\FormHelperService;
use Common\Controller\Lva\Traits\CrudTableTrait;
use Common\Controller\Traits\GenericBusinessDetails;

/**
 * Shared logic between Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessDetailsController extends AbstractController
{
    use CrudTableTrait;
    use GenericBusinessDetails;

    protected $section = 'business_details';

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
        $orgData = $this->getServiceLocator()->get('Entity\Organisation')->getBusinessDetailsData($orgId);
        $natureOfBusiness = $this->getServiceLocator()
            ->get('Entity\OrganisationNatureOfBusiness')
            ->getAllForOrganisationForSelect($orgId);

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($orgData, $natureOfBusiness);
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

            /**
             * Note that we can't return early here; the first two conditions
             * still fall out of their respective branches and render the form
             */
            if (isset($data['data']['companyNumber']['submit_lookup_company'])) {
                $this->processCompanyLookup($data, $form, 'data');
            } elseif (isset($tradingNames['submit_add_trading_name'])) {
                $this->processTradingNames($tradingNames, $form);
            } elseif ($form->isValid()) {

                $this->processSave($tradingNames, $orgId, $data);
                $this->postSave('business_details');

                // we can't always assume this index exists; varies depending on
                // org type
                $crudTables = isset($data['table']) ? array($data['table']) : array();
                $crudAction = $this->getCrudAction($crudTables);

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('business_details');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('business_details', $form);
    }

    /**
     * User has pressed 'Add another' on trading names
     */
    private function processTradingNames($tradingNames, $form)
    {
        $form->setValidationGroup(array('data' => ['tradingNames']));

        if ($form->isValid()) {

            // remove existing entries from collection and check for empty entries
            $names = [];
            foreach ($tradingNames['trading_name'] as $val) {
                $trimmedVal = trim($val);
                if (!empty($trimmedVal)) {
                    $names[] = $val;
                }
            }
            $names[] = '';

            $form->get('data')->get('tradingNames')->get('trading_name')->populateValues($names);
        }
    }

    /**
     * Normal submission; save the form data
     */
    private function processSave($tradingNames, $orgId, $data)
    {
        if (isset($tradingNames['trading_name']) && !empty($tradingNames['trading_name'])) {
            $tradingNames = $this->formatTradingNamesDataForSave($orgId, $data);
            $this->getServiceLocator()->get('Entity\TradingNames')->save($tradingNames);
        }

        $registeredAddressId = null;

        if (isset($data['data']['registeredAddress'])) {
            $registeredAddressId = $this->saveRegisteredAddress($orgId, $data['data']['registeredAddress']);
        }

        $this->saveNatureOfBusiness($orgId, $data['data']['natureOfBusiness']);

        $saveData = $this->formatDataForSave($data);
        $saveData['id'] = $orgId;

        if ($registeredAddressId !== null) {
            $saveData['contactDetails'] = $registeredAddressId;
        }

        $this->getServiceLocator()->get('Entity\Organisation')->save($saveData);
    }

    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    private function addOrEdit($mode)
    {
        $orgId = $this->getCurrentOrganisationId();
        $request = $this->getRequest();

        $data = array();
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->formatCrudDataForForm(
                $this->getServiceLocator()->get('Entity\CompanySubsidiary')->getById($this->params('child_id'))
            );
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\BusinessDetailsSubsidiaryCompany', $this->getRequest())
            ->setData($data);

        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        if ($request->isPost() && $form->isValid()) {
            $data = $this->formatCrudDataForSave($data);
            $data['organisation'] = $orgId;

            $this->getServiceLocator()->get('Entity\CompanySubsidiary')->save($data);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_subsidiary_company', $form);
    }

    private function formatTradingNamesDataForSave($organisationId, $data)
    {
        $tradingNames = [];

        foreach ($data['data']['tradingNames']['trading_name'] as $tradingName) {
            if (trim($tradingName) !== '') {
                $tradingNames[] = [
                    'name' => $tradingName
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
     * @param array $natureOfBusiness
     * @return array
     */
    private function formatDataForForm($data, $natureOfBusiness)
    {
        $tradingNames = array();
        foreach ($data['tradingNames'] as $tradingName) {
            $tradingNames[] = $tradingName['name'];
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
                'type' => $data['type']['id'],
                'registeredAddress' => $data['contactDetails']['address'],
                'natureOfBusiness' => $natureOfBusiness
            )
        );
    }

    /**
     * Format subsidiary data for save
     *
     * @param array $data
     * return array
     */
    private function formatCrudDataForSave($data)
    {
        return $data['data'];
    }

    /**
     * Format subsidiary data for form
     *
     * @param array $data
     * return array
     */
    private function formatCrudDataForForm($data)
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
        $this->alterFormForLva($form);

        $orgType = $data['type']['id'];

        $fieldset = $form->get('data');

        // have to manually link up the edit button next to
        // the business type dropdown
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
            case OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY:
            case OrganisationEntityService::ORG_TYPE_LLP:
                // no-op; the full form is fine
                break;

            case OrganisationEntityService::ORG_TYPE_SOLE_TRADER:
                $this->alterFormForNonRegisteredCompany($form);
                $this->getServiceLocator()->get('Helper\Form')
                    ->remove($form, 'data->name');
                break;

            case OrganisationEntityService::ORG_TYPE_PARTNERSHIP:
                $this->alterFormForNonRegisteredCompany($form);
                $this->appendToLabel($fieldset->get('name'), '.partnership');
                break;

            case OrganisationEntityService::ORG_TYPE_OTHER:
                $this->getServiceLocator()->get('Helper\Form')
                    ->remove($form, 'data->tradingNames');
                $this->alterFormForNonRegisteredCompany($form);
                $this->appendToLabel($fieldset->get('name'), '.other');
                break;
        }

        return $form;
    }

    /**
     * Append to an element label
     *
     * @param \Zend\Form\Element $element
     * @param string $append
     */
    private function appendToLabel($element, $append)
    {
        $this->getServiceLocator()->get('Helper\Form')
            ->alterElementLabel($element, $append, FormHelperService::ALTER_LABEL_APPEND);
    }

    /**
     * Make generic form alterations for non limited (or LLP) companies
     *
     * @param \Zend\Form\Form $form
     */
    private function alterFormForNonRegisteredCompany($form)
    {
        $this->getServiceLocator()->get('Helper\Form')->remove($form, 'table')
            ->remove($form, 'data->companyNumber')
            ->remove($form, 'data->registeredAddress');
    }

    private function populateTable($form, $orgId)
    {
        $tableData = $this->getServiceLocator()->get('Entity\CompanySubsidiary')
            ->getAllForOrganisation($orgId);

        $table = $this->getServiceLocator()
            ->get('Table')
            ->prepareTable('lva-subsidiaries', $tableData);

        $form->get('table')
            ->get('table')
            ->setTable($table);
    }

    /**
     * Mechanism to *actually* delete a subsidiary, invoked by the
     * underlying delete action
     */
    protected function delete()
    {
        $id = $this->params('child_id');
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            $this->getServiceLocator()->get('Entity\CompanySubsidiary')->delete($id);
        }
    }
}
