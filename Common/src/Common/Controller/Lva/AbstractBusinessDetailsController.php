<?php

/**
 * Shared logic between Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Traits\CrudTableTrait;
use Common\Data\Mapper\Lva\BusinessDetails as Mapper;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateBusinessDetails;
use Dvsa\Olcs\Transfer\Command\Application\UpdateBusinessDetails as ApplicationUpdateBusinessDetails;
use Dvsa\Olcs\Transfer\Query\CompanySubsidiary\CompanySubsidiary;
use Dvsa\Olcs\Transfer\Query\Licence\BusinessDetails;
use Common\Data\Mapper\Lva\CompanySubsidiary as CompanySubsidiaryMapper;
use Zend\Form\Form;

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

        $response = $this->handleQuery(BusinessDetails::create(['id' => $this->getLicenceId()]));

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
            return $this->notFoundAction();
        }

        $orgData = $response->getResult();

        if ($request->isPost()) {
            $data = $this->getFormPostData($orgData);
        } else {
            $data = Mapper::mapFromResult($orgData);
        }

        // Gets a fully configured/altered form for any version of this section
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm($orgData['type']['id'], $orgData['hasInforceLicences'])
            ->setData($data);

        if ($form->has('table')) {
            $this->populateTable($form, $orgData);
        }

        // Added an early return for non-posts to improve the readability of the code
        if (!$request->isPost()) {
            return $this->renderForm($form);
        }

        // If we are performing a company number lookup
        if (isset($data['data']['companyNumber']['submit_lookup_company'])) {

            $this->getServiceLocator()->get('Helper\Form')
                ->processCompanyNumberLookupForm($form, $data, 'data', 'registeredAddress');

            return $this->renderForm($form);
        }

        // We'll re-use this in a few places, so cache the lookup just for the sake of legibility
        $tradingNames = isset($data['data']['tradingNames']) ? $data['data']['tradingNames'] : [];

        // If we are interacting with the trading names collection element
        if (isset($tradingNames['submit_add_trading_name'])) {

            $this->processTradingNames($tradingNames, $form);
            return $this->renderForm($form);
        }

        $crudAction = null;

        if (isset($data['table'])) {
            $crudAction = $this->getCrudAction([$data['table']]);
        }

        if ($crudAction !== null) {
            $formHelper = $this->getServiceLocator()->get('Helper\Form');
            $formHelper->disableValidation($form->getInputFilter());
        }

        // If our form is invalid, render the form to display the errors
        if (!$form->isValid()) {
            return $this->renderForm($form);
        }

        if ($this->lva === 'licence') {
            $dtoData = [
                'id' => $this->getLicenceId(),
                'version' => $data['version'],
                'name' => $data['data']['name'],
                'tradingNames' => isset($tradingNames['trading_name']) ? $tradingNames['trading_name'] : [],
                'natureOfBusiness' => isset($data['data']['natureOfBusiness'])
                    ? $data['data']['natureOfBusiness'] : null,
                'companyOrLlpNo' => isset($data['data']['companyNumber']['company_number'])
                    ? $data['data']['companyNumber']['company_number'] : null,
                'registeredAddress' => isset($data['registeredAddress']) ? $data['registeredAddress'] : null,
                'partial' => $crudAction !== null
            ];

            $response = $this->handleCommand(UpdateBusinessDetails::create($dtoData));
        } else {
            $dtoData = [
                'id' => $this->getIdentifier(),
                'licence' => $this->getLicenceId(),
                'version' => $data['version'],
                'name' => $data['data']['name'],
                'tradingNames' => isset($tradingNames['trading_name']) ? $tradingNames['trading_name'] : [],
                'natureOfBusiness' => isset($data['data']['natureOfBusiness'])
                    ? $data['data']['natureOfBusiness'] : null,
                'companyOrLlpNo' => isset($data['data']['companyNumber']['company_number'])
                    ? $data['data']['companyNumber']['company_number'] : null,
                'registeredAddress' => isset($data['registeredAddress']) ? $data['registeredAddress'] : null,
                'partial' => $crudAction !== null
            ];

            $response = $this->handleCommand(ApplicationUpdateBusinessDetails::create($dtoData));
        }

        if (!$response->isOk()) {

            $this->mapErrors($form, $response->getResult()['messages']);

            return $this->renderForm($form);
        }

        if ($crudAction !== null) {
            return $this->handleCrudAction($crudAction);
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
            || !array_key_exists('company_number', $data['data']['companyNumber'])
        ) {
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

            $response = $this->handleQuery(CompanySubsidiary::create(['id' => $id]));

            if ($response->isClientError()) {
                return $this->notFoundAction();
            }

            $data = CompanySubsidiaryMapper::mapFromResult($response->getResult());
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

            $dtoData = [
                $this->getIdentifierIndex() => $this->getIdentifier(),
                'name' => $data['data']['name'],
                'companyNo' => $data['data']['companyNo'],
            ];

            if ($this->lva === 'licence') {
                $lvaNamespace = 'Licence';
            } else {
                $lvaNamespace = 'Application';
            }

            // Creating
            if ($id !== null) {
                $dtoData['id'] = $id;
                $dtoData['version'] = $data['data']['version'];
                $dtoClass = sprintf('\Dvsa\Olcs\Transfer\Command\%s\UpdateCompanySubsidiary', $lvaNamespace);
            } else {
                $dtoClass = sprintf('\Dvsa\Olcs\Transfer\Command\%s\CreateCompanySubsidiary', $lvaNamespace);
            }

            $response = $this->handleCommand($dtoClass::create($dtoData));

            if ($response->isOk()) {
                return $this->handlePostSave(null, false);
            }

            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        return $this->render($mode . '_subsidiary_company', $form);
    }

    protected function populateTable($form, $orgData)
    {
        $table = $this->getServiceLocator()->get('Table')
            ->prepareTable('lva-subsidiaries', $orgData['companySubsidiaries']);

        $this->getServiceLocator()->get('Helper\Form')->populateFormTable($form->get('table'), $table);
    }

    /**
     * Mechanism to *actually* delete a subsidiary, invoked by the underlying delete action
     */
    protected function delete()
    {
        $id = $this->params('child_id');
        $ids = explode(',', $id);

        $data = [
            'ids' => $ids,
            $this->getIdentifierIndex() => $this->getIdentifier()
        ];

        if ($this->lva === 'licence') {
            $lvaNamespace = 'Licence';
        } else {
            $lvaNamespace = 'Application';
        }

        $dtoClass = sprintf('\Dvsa\Olcs\Transfer\Command\%s\DeleteCompanySubsidiary', $lvaNamespace);

        $response = $this->handleCommand($dtoClass::create($data));

        if (!$response->isOk()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }
    }

    protected function mapErrors(Form $form, array $errors)
    {
        $formMessages = [];

        if (isset($errors['natureOfBusiness'])) {
            $formMessages['data']['natureOfBusiness'] = $errors['natureOfBusiness'];
            unset($errors['natureOfBusiness']);
        }

        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
