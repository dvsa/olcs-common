<?php

/**
 * Shared logic between Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Traits\CrudTableTrait;
use Common\BusinessService\Response;

/**
 * Shared logic between Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessDetailsController extends AbstractController
{
    use CrudTableTrait;

    protected $section = 'business_details';

    /**
     * Business details section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        $orgId = $this->getCurrentOrganisationId();

        $organisationEntity = $this->getServiceLocator()->get('Entity\Organisation');

        // we *always* want to get org data because we rely on it in
        // alterForm which is called irrespective of whether we're doing
        // a GET or a POST
        $orgData = $organisationEntity->getBusinessDetailsData($orgId);

        if ($request->isPost()) {
            $data = $this->getFormPostData($orgData);
        } else {
            $data = $this->formatDataForForm($orgData, $organisationEntity->getNatureOfBusinessesForSelect($orgId));
        }

        // Get's a fully configured/altered form for any version of this section
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm($orgData['type']['id'], $orgId)
            ->setData($data);

        if ($form->has('table')) {
            $this->populateTable($form);
        }

        // Added an early return for non-posts to improve the readability of the code
        if (!$request->isPost()) {
            return $this->renderForm($form);
        }

        // If we are performing a company number lookup
        if (isset($data['data']['companyNumber']['submit_lookup_company'])) {
            $this->getServiceLocator()->get('Helper\Form')->processCompanyNumberLookupForm($form, $data, 'data');
            return $this->renderForm($form);
        }

        // We'll re-use this in a few places, so cache the lookup just for the sake of legibility
        $tradingNames = isset($data['data']['tradingNames']) ? $data['data']['tradingNames'] : array();

        // If we are interacting with the trading names collection element
        if (isset($tradingNames['submit_add_trading_name'])) {
            $this->processTradingNames($tradingNames, $form);
            return $this->renderForm($form);
        }

        // If our form is invalid, render the form to display the errors
        if (!$form->isValid()) {
            return $this->renderForm($form);
        }

        // If we have gotten to here, then we want to start persisting
        $tradingNamesToProcess = [];
        if (isset($tradingNames['trading_name'])) {
            $tradingNamesToProcess = $tradingNames['trading_name'];
        }

        $response = $this->getServiceLocator()->get('BusinessServiceManager')
            ->get('Lva\BusinessDetails')
            ->process(
                [
                    'tradingNames' => $tradingNamesToProcess,
                    'orgId' => $orgId,
                    'data' => $data,
                    'licenceId' => $this->getLicenceId()
                ]
            );

        if (!$response->isOk()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($response->getMessage());
            return $this->renderForm($form);
        }

        $this->postSave('business_details');

        // If we have a table, then we may have a crud action to handle
        if (isset($data['table'])) {
            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }
        }

        return $this->completeSection('business_details');
    }

    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * Method used to render the indexAction form
     *
     * @param Zend\Form\Form $form
     * @return Zend\View\Model\ViewModel
     */
    protected function renderForm($form)
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');
        return $this->render('business_details', $form);
    }

    /**
     * Grabs the data from the post, and set's some defaults in-case there are disabled fields
     *
     * @param array $orgData
     * @return array
     */
    protected function getFormPostData($orgData)
    {
        $data = (array)$this->getRequest()->getPost();

        if (!isset($data['data']['companyNumber'])
            || !array_key_exists('company_number', $data['data']['companyNumber'])) {
            $data['data']['companyNumber']['company_number'] = $orgData['companyOrLlpNo'];
        }

        if (!array_key_exists('name', $data['data'])) {
            $data['data']['name'] = $orgData['name'];
        }

        return $data;
    }

    /**
     * User has pressed 'Add another' on trading names
     * So we need to duplicate the trading names field to produce another input
     */
    protected function processTradingNames($tradingNames, $form)
    {
        $form->setValidationGroup(array('data' => ['tradingNames']));

        if ($form->isValid()) {

            // remove existing entries from collection and check for empty entries
            $names = [];
            foreach ($tradingNames['trading_name'] as $val) {
                $trimmedVal = trim($val);
                if (!empty($trimmedVal)) {
                    $names[] = $trimmedVal;
                }
            }
            $names[] = '';

            $form->get('data')->get('tradingNames')->get('trading_name')->populateValues($names);
        }
    }

    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();

        $id = $this->params('child_id');

        $data = [];

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = ['data' => $this->getServiceLocator()->get('Entity\CompanySubsidiary')->getById($id)];
        }

        // @todo Move this into a form service
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\BusinessDetailsSubsidiaryCompany', $request)
            ->setData($data);

        // @todo Add this generic behaviour to a form service
        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        if ($request->isPost() && $form->isValid()) {

            $data['id'] = $id;
            $data['licenceId'] = $this->getLicenceId();

            $response = $this->getServiceLocator()->get('BusinessServiceManager')
                ->get('Lva\CompanySubsidiary')->process($data);

            if ($response->isOk()) {
                return $this->handlePostSave();
            }

            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($response->getMessage());
        }

        return $this->render($mode . '_subsidiary_company', $form);
    }

    /**
     * Format data for form (This is presentation logic)
     *
     * @param array $data
     * @param array $natureOfBusiness
     * @return array
     */
    protected function formatDataForForm($data, $natureOfBusiness)
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

    protected function populateTable($form)
    {
        $tableData = $this->getServiceLocator()->get('Entity\CompanySubsidiary')->getForLicence($this->getLicenceId());

        $table = $this->getServiceLocator()->get('Table')->prepareTable('lva-subsidiaries', $tableData);

        $this->getServiceLocator()->get('Helper\Form')->populateFormTable($form->get('table'), $table);
    }

    /**
     * Mechanism to *actually* delete a subsidiary, invoked by the underlying delete action
     */
    protected function delete()
    {
        $id = $this->params('child_id');
        $ids = explode(',', $id);

        $params = [
            'ids' => $ids,
            'licenceId' => $this->getLicenceId()
        ];

        $response = $this->getServiceLocator()->get('BusinessServiceManager')->get('Lva\DeleteCompanySubsidiary')
            ->process($params);

        if (!$response->isOk()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($response->getMessage());
        }
    }
}
