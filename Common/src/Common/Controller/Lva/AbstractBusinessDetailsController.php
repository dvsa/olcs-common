<?php

/**
 * Shared logic between Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Traits\CrudTableTrait;
use Common\Controller\Traits\GenericBusinessDetails;
use Common\Controller\Lva\Interfaces\AdapterAwareInterface;

/**
 * Shared logic between Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessDetailsController extends AbstractController implements AdapterAwareInterface
{
    use CrudTableTrait,
        GenericBusinessDetails,
        Traits\AdapterAwareTrait;

    protected $section = 'business_details';

    /**
     * Business details section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        $formService = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section);

        $orgId = $this->getCurrentOrganisationId();

        $organisationEntity = $this->getServiceLocator()->get('Entity\Organisation');

        // we *always* want to get org data because we rely on it in
        // alterForm which is called irrespective of whether we're doing
        // a GET or a POST
        $orgData = $organisationEntity->getBusinessDetailsData($orgId);

        $form = $formService->getForm($orgData['type']['id']);




        if ($request->isPost()) {
            $data = (array)$request->getPost();

            if (!isset($data['data']['companyNumber'])
                || !array_key_exists('company_number', $data['data']['companyNumber'])) {
                $data['data']['companyNumber']['company_number'] = $orgData['companyOrLlpNo'];
            }

            if (!array_key_exists('name', $data['data'])) {
                $data['data']['name'] = $orgData['name'];
            }

        } else {
            $data = $this->formatDataForForm($orgData, $organisationEntity->getNatureOfBusinessesForSelect($orgId));
        }

        $form->setData($data);

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
        $adapter = $this->getAdapter();

        $registeredAddressId = null;
        $isDirty = false;

        if (isset($tradingNames['trading_name']) && !empty($tradingNames['trading_name'])) {
            $tradingNames = $this->formatTradingNamesDataForSave($orgId, $data);

            $isDirty = $adapter->hasChangedTradingNames($orgId, $tradingNames['tradingNames']);
            $this->getServiceLocator()->get('Entity\TradingNames')->save($tradingNames);
        }

        if (isset($data['registeredAddress'])) {
            $isDirty = $isDirty ?: $adapter->hasChangedRegisteredAddress($orgId, $data['registeredAddress']);
            $registeredAddressId = $this->saveRegisteredAddress($orgId, $data['registeredAddress']);
        }

        $isDirty = $isDirty ?: $adapter->hasChangedNatureOfBusiness($orgId, $data['data']['natureOfBusinesses']);

        $saveData = $this->formatDataForSave($data);
        $saveData['id'] = $orgId;

        if ($registeredAddressId !== null) {
            $saveData['contactDetails'] = $registeredAddressId;
        }

        if ($isDirty) {
            $adapter->postSave(
                [
                    'licence' => $this->getLicenceId(),
                    'user' => $this->getLoggedInUser()
                ]
            );
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
        $adapter = $this->getAdapter();

        $orgId = $this->getCurrentOrganisationId();
        $request = $this->getRequest();
        $id = $this->params('child_id');
        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {

            $data = $this->formatCrudDataForForm(
                $this->getServiceLocator()->get('Entity\CompanySubsidiary')->getById($id)
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

            if ($id === null || $adapter->hasChangedSubsidiaryCompany($id, $data)) {
                $adapter->postCrudSave(
                    $id === null ? 'added' : 'updated',
                    [
                        'licence' => $this->getLicenceId(),
                        'user'    => $this->getLoggedInUser(),
                        'name'    => $data['name']
                    ]
                );
            }

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
        $persist = array(
            'version' => $data['version']
        );

        $data = $data['data'];

        if (isset($data['companyNumber']['company_number'])) {
            $persist['companyOrLlpNo'] = $data['companyNumber']['company_number'];
        }

        if (isset($data['name'])) {
            $persist['name'] = $data['name'];
        }

        $persist['natureOfBusinesses'] = $data['natureOfBusinesses'];

        return $persist;
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
                'natureOfBusinesses' => $natureOfBusiness
            ),
            'registeredAddress' => $data['contactDetails']['address'],
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
        $adapter = $this->getAdapter();

        $id = $this->params('child_id');
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            $company = $this->getServiceLocator()
                ->get('Entity\CompanySubsidiary')
                ->getById($id);

            $this->getServiceLocator()
                ->get('Entity\CompanySubsidiary')
                ->delete($id);

            $adapter->postCrudSave(
                'deleted',
                [
                    'licence' => $this->getLicenceId(),
                    'user'    => $this->getLoggedInUser(),
                    'name'    => $company['name']
                ]
            );
        }
    }
}
